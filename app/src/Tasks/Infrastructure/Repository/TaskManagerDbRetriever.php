<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Collection\TaskLinkCollection;
use App\Tasks\Domain\DTO\TaskDTO;
use App\Tasks\Domain\DTO\TaskManagerDTO;
use App\Tasks\Domain\Factory\TaskFactory;
use App\Tasks\Domain\Factory\TaskManagerFactory;
use App\Tasks\Domain\ValueObject\TaskLink;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final class TaskManagerDbRetriever
{
    public function __construct(
        private readonly TaskManagerFactory $taskManagerFactory,
        private readonly TaskFactory $taskFactory,
    ) {
    }

    /**
     * @param QueryBuilder $builder
     * @return array
     * @throws Exception
     */
    public function retrieveAll(QueryBuilder $builder): array
    {
        $rawProjects = $builder->fetchAllAssociative();

        $result = [];
        foreach ($rawProjects as $rawProject) {
            $result[] = $this->retrieve($builder, $rawProject);
        }
        return $result;
    }

    /**
     * @param QueryBuilder $builder
     * @return array
     * @throws Exception
     */
    public function retrieveOne(QueryBuilder $builder): array
    {
        $rawManager = $builder->fetchAssociative();
        if ($rawManager === false) {
            return [null, 0];
        }

        return $this->retrieve($builder, $rawManager);
    }

    /**
     * @param QueryBuilder $builder
     * @param array $rawManager
     * @return array
     * @throws Exception
     */
    private function retrieve(QueryBuilder $builder, array $rawManager): array
    {
        $rawManager['participant_ids'] = $this->retrieveParticipants($builder, $rawManager['id']);
        $rawManager['tasks'] = $this->retrieveTasks($builder, $rawManager['id']);

        return [$this->taskManagerFactory->create(TaskManagerDTO::create($rawManager)), $rawManager['version']];
    }

    /**
     * @param QueryBuilder $builder
     * @param string $managerId
     * @return UserIdCollection
     * @throws Exception
     */
    private function retrieveParticipants(QueryBuilder $builder, string $managerId): UserIdCollection
    {
        $this->resetBuilder($builder);

        $rawParticipants = $builder
            ->select('user_id')
            ->from('task_manager_participants')
            ->where('task_manager_id = ?')
            ->setParameters([$managerId])
            ->fetchFirstColumn();
        return new UserIdCollection(
            array_map(fn(string $id) => new UserId($id), $rawParticipants)
        );
    }

    /**
     * @param QueryBuilder $builder
     * @param string $managerId
     * @return TaskCollection
     * @throws Exception
     */
    private function retrieveTasks(QueryBuilder $builder, string $managerId): TaskCollection
    {
        $this->resetBuilder($builder);

        $rawTasks = $builder
            ->select('*')
            ->from('tasks')
            ->where('task_manager_id = ?')
            ->setParameters([$managerId])
            ->fetchAllAssociative();
        return new TaskCollection(
            array_map(function (array $item) use ($builder) {
                $this->resetBuilder($builder);
                $rawLinks = $builder
                    ->select('*')
                    ->from('task_links')
                    ->where('from_task_id = ?')
                    ->setParameters([$item['id']])
                    ->fetchAllAssociative();

                $item['links'] = new TaskLinkCollection(
                    array_map(function (array $rawLink) {
                        return new TaskLink(
                            new TaskId($rawLink['to_task_id'])
                        );
                    }, $rawLinks)
                );
                return $this->taskFactory->create(TaskDTO::create($item));
            }, $rawTasks)
        );
    }

    private function resetBuilder(QueryBuilder $criteria): void
    {
        $criteria->resetQueryParts();
    }
}
