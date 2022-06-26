<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectId;
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
        return $this->repository()->find($id);
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