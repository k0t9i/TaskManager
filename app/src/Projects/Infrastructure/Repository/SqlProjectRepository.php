<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlProjectRepository implements ProjectRepositoryInterface
{
    use OptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjectDbRetriever $dbRetriever
    ) {
    }

    /**
     * @param ProjectId $id
     * @return Project|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?Project
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->from('projects')
            ->where('id = ?')
            ->setParameters([$id->value]);

        /** @var Project $project */
        [$project, $version] = $this->dbRetriever->retrieveOne($builder);
        if ($project !== null) {
            $this->saveVersion($project->getId()->value, $version);
        }
        return $project;
    }

    /**
     * @param Project $project
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(Project $project): void
    {
        $version = $this->getVersion($project->getId());
        $isExist = $version > 0;
        $this->ensureIsVersionLesserThanPrevious($project->getId()->value, $version);
        $version += 1;

        $participants = $project->getParticipants()->getInnerItems();
        $this->insertParticipants($participants, $project->getId()->value);
        $this->deleteParticipants($participants, $project->getId()->value);
        $participants->flush();

        $tasks = $project->getTasks()->getInnerItems();
        $this->insertTasks($tasks, $project->getId()->value);
        $this->updateTasks($tasks, $project->getId()->value);
        $tasks->flush();

        if ($isExist) {
            $this->updateProject($project, $version);
        } else {
            $this->insertProject($project, $version);
        }
    }

    /**
     * @param ProjectId $id
     * @return int
     * @throws Exception
     */
    private function getVersion(ProjectId $id): int
    {
        $version = $this->queryBuilder()
            ->select('version')
            ->from('projects')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchOne();
        return $version ?: 0;
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }

    /**
     * @param UserIdCollection $participants
     * @param string $projectId
     * @throws Exception
     */
    private function insertParticipants(UserIdCollection $participants, string $projectId): void
    {
        /** @var UserId $item */
        foreach ($participants->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('project_participants')
                ->values([
                    'project_id' => '?',
                    'user_id' => '?',
                ])
                ->setParameters([
                    $projectId,
                    $item->value
                ])
                ->executeStatement();
        }
    }

    /**
     * @param UserIdCollection $participants
     * @param string $projectId
     * @throws Exception
     */
    private function deleteParticipants(UserIdCollection $participants, string $projectId): void
    {
        $deleted = array_map(fn(UserId $id) => $id->value, $participants->getDeleted());
        $this->queryBuilder()
            ->delete('project_participants')
            ->where('project_id = ?')
            ->andWhere('user_id in (?)')
            ->setParameters([
                $projectId,
                $deleted
            ], [
                1 => Connection::PARAM_STR_ARRAY
            ])
            ->executeStatement();
    }

    /**
     * @param ProjectTaskCollection $tasks
     * @param string $projectId
     * @throws Exception
     */
    private function insertTasks(ProjectTaskCollection $tasks, string $projectId): void
    {
        /** @var ProjectTask $item */
        foreach ($tasks->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('project_tasks')
                ->values([
                    'id' => '?',
                    'project_id' => '?',
                    'task_id' => '?',
                    'owner_id' => '?',
                ])
                ->setParameters([
                    $item->getId()->value,
                    $projectId,
                    $item->getTaskId()->value,
                    $item->getOwnerId()->value
                ])
                ->executeStatement();
        }
    }

    /**
     * @param ProjectTaskCollection $tasks
     * @param string $projectId
     * @throws Exception
     */
    private function updateTasks(ProjectTaskCollection $tasks, string $projectId): void
    {
        /** @var ProjectTask $item */
        foreach ($tasks->getUpdated() as $item) {
            $this->queryBuilder()
                ->update('project_tasks')
                ->set('project_id', '?')
                ->set('task_id', '?')
                ->set('owner_id', '?')
                ->where('id = ?')
                ->setParameters([
                    $projectId,
                    $item->getTaskId()->value,
                    $item->getOwnerId()->value,
                    $item->getId()->value
                ])
                ->executeStatement();
        }
    }

    /**
     * @param Project $project
     * @param int $version
     * @throws Exception
     */
    private function updateProject(Project $project, int $version): void
    {
        $this->queryBuilder()
            ->update('projects')
            ->set('name', '?')
            ->set('description', '?')
            ->set('finish_date', '?')
            ->set('status', '?')
            ->set('owner_id', '?')
            ->set('version', '?')
            ->where('id = ?')
            ->setParameters([
                $project->getInformation()->name->value,
                $project->getInformation()->description->value,
                $project->getInformation()->finishDate->getValue(),
                $project->getStatus()->getScalar(),
                $project->getOwner()->userId->value,
                $version,
                $project->getId()->value
            ])
            ->executeStatement();
    }

    /**
     * @param Project $project
     * @param int $version
     * @throws Exception
     */
    private function insertProject(Project $project, int $version): void
    {
        $this->queryBuilder()
            ->insert('projects')
            ->values([
                'id' => '?',
                'name' => '?',
                'description' => '?',
                'finish_date' => '?',
                'status' => '?',
                'owner_id' => '?',
                'version' => '?'
            ])
            ->setParameters([
                $project->getId()->value,
                $project->getInformation()->name->value,
                $project->getInformation()->description->value,
                $project->getInformation()->finishDate->getValue(),
                $project->getStatus()->getScalar(),
                $project->getOwner()->userId->value,
                $version
            ])
            ->executeStatement();
    }
}