<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Tasks\Domain\DTO\TaskListResponseDTO;
use App\Tasks\Domain\DTO\TaskResponseDTO;
use App\Tasks\Domain\Repository\TaskQueryRepositoryInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SqlTaskQueryRepository implements TaskQueryRepositoryInterface
{
    private const CONNECTION = 'read';

    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * @param ProjectId $projectId
     * @param UserId $userId
     * @return TaskListResponseDTO[]
     * @throws Exception
     */
    public function findAllByProjectIdAndUserId(ProjectId $projectId, UserId $userId): array
    {
        $rawItems = $this->queryBuilder()
            ->select('t.*')
            ->from('task_projections', 't')
            // join for lazybone
            ->leftJoin('t', 'project_projections', 'p', 'p.id = t.project_id')
            ->where('t.project_id = ?')
            ->andWhere('p.user_id = ?')
            ->setParameters([
                $projectId->value,
                $userId->value,
            ])
            ->fetchAllAssociative();

        $result = [];
        foreach ($rawItems as $rawItem) {
            $result[] = TaskListResponseDTO::create($rawItem);
        }

        return $result;
    }

    /**
     * @param TaskId $id
     * @param UserId $userId
     * @return TaskResponseDTO|null
     * @throws Exception
     */
    public function findByIdAndUserId(TaskId $id, UserId $userId): ?TaskResponseDTO
    {
        $rawItem = $this->queryBuilder()
            ->select('t.*')
            ->from('task_projections', 't')
            // join for lazybone
            ->leftJoin('t', 'project_projections', 'p', 'p.id = t.project_id')
            ->where('t.id = ?')
            ->andWhere('p.user_id = ?')
            ->setParameters([
                $id->value,
                $userId->value
            ])
            ->fetchAssociative();
        if ($rawItem === false) {
            return null;
        }

        return TaskResponseDTO::create($rawItem);
    }

    /**
     * @param TaskId $id
     * @return TaskResponseDTO|null
     * @throws Exception
     */
    public function findById(TaskId $id): ?TaskResponseDTO
    {
        $rawItem = $this->queryBuilder()
            ->select('*')
            ->from('task_projections')
            ->where('id = ?')
            ->setParameters([
                $id->value
            ])
            ->fetchAssociative();
        if ($rawItem === false) {
            return null;
        }

        return TaskResponseDTO::create($rawItem);
    }

    private function queryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::CONNECTION);
        return $connection->createQueryBuilder();
    }
}