<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Collection\ProjectParticipantCollection;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Exception\ProjectNotExistException;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectParticipant;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\TaskCollection;
use App\Users\Domain\ValueObject\UserId;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param ProjectId $id
     * @return Project|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?Project
    {
        /** @var Project $project */
        $project = $this->repository()->find($id);
        if ($project !== null) {
            $rawParticipants = $this->entityManager->getConnection()->createQueryBuilder()
                ->select('*')
                ->from('project_participant', 'p')
                ->where('p.project_id = :id')
                ->setParameter('id', $project->getId()->value)
                ->executeQuery()
                ->fetchAllAssociative();
            $participants = [];
            foreach ($rawParticipants as $participant) {
                $participants[$participant['user_id']] = new ProjectParticipant(
                    new UserId($participant['user_id'])
                );
            }
            $project->setParticipants(new ProjectParticipantCollection($participants));

            /** @var Task[] $rawTasks */
            $rawTasks = $this->entityManager->getRepository(Task::class)->findBy([
                'project' => $project
            ]);
            $tasks = [];
            foreach ($rawTasks as $task) {
                $tasks[$task->getId()->value] = $task;
            }
            $project->setTasks(new TaskCollection($tasks));
        }

        return $project;
    }

    /**
     * @param ProjectId $id
     * @return Project
     * @throws Exception
     */
    public function getById(ProjectId $id): Project
    {
        $project = $this->findById($id);
        if ($project === null) {
            throw new ProjectNotExistException();
        }
        return $project;
    }

    public function findByOwnerId(string $ownerId): ?Project
    {
        // TODO: Implement findByOwnerId() method.
        return null;
    }

    public function create(Project $project): void
    {
        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    /**
     * @param Project $project
     * @throws Exception
     */
    public function update(Project $project): void
    {
        $participants = $project->getParticipants();
        if ($participants->isDirty()) {
            /** @var ProjectParticipant $item */
            foreach ($participants->getAdded() as $item) {
                $this->entityManager->getConnection()->createQueryBuilder()
                    ->insert('project_participant')
                    ->values([
                        'project_id' => '?',
                        'user_id' => '?',
                    ])
                    ->setParameters([
                        $project->getId()->value,
                        $item->userId->value
                    ])
                    ->executeStatement();
            }
            /** @var ProjectParticipant $item */
            foreach ($participants->getDeleted() as $item) {
                $this->entityManager->getConnection()->createQueryBuilder()
                    ->delete('project_participant')
                    ->where('project_id = ?')
                    ->andWhere('user_id = ?')
                    ->setParameters([
                        $project->getId()->value,
                        $item->userId->value
                    ])
                    ->executeStatement();
            }
            $participants->flush();
        }

        $tasks = $project->getTasks();
        foreach ($tasks as $task) {
            $this->entityManager->persist($task);
        }
        foreach ($tasks->getDeleted() as $deletedTask) {
            $this->entityManager->remove($deletedTask);
        }
        $tasks->flush();

        $this->entityManager->persist($project);
        $this->entityManager->flush();
    }

    public function delete(Project $project): void
    {
        $participants = $project->getParticipants();
        foreach ($participants as $participant) {
            $this->entityManager->getConnection()->createQueryBuilder()
                ->delete('project_participant')
                ->where('project_id = ?')
                ->andWhere('user_id = ?')
                ->setParameters([
                    $project->getId()->value,
                    $participant->userId->value
                ])
                ->executeStatement();
        }
        foreach ($project->getTasks() as $task) {
            $this->entityManager->remove($task);
        }

        $this->entityManager->remove($project);
        $this->entityManager->flush();
    }

    private function repository(): EntityRepository
    {
        return $this->entityManager->getRepository(Project::class);
    }
}