<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Projects\Domain\DTO\ProjectResponseDTO;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SqlProjectQueryRepository implements ProjectQueryRepositoryInterface
{
    private const CONNECTION = 'read';

    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {
    }

    /**
     * @param UserId $userId
     * @return ProjectListResponseDTO[]
     * @throws Exception
     */
    public function findAllByUserId(UserId $userId): array
    {
        $rawItems = $this->queryBuilder()
            ->select('*')
            ->from('project_projections')
            ->where('user_id = ?')
            ->setParameters([$userId->value])
            ->fetchAllAssociative();

        $result = [];
        foreach ($rawItems as $rawItem) {
            $result[] = ProjectListResponseDTO::create($rawItem);
        }

        return $result;
    }

    /**
     * @param ProjectId $id
     * @param UserId $userId
     * @return ProjectResponseDTO|null
     * @throws Exception
     */
    public function findByIdAndUserId(ProjectId $id, UserId $userId): ?ProjectResponseDTO
    {
        $rawItem = $this->queryBuilder()
            ->select('*')
            ->from('project_projections')
            ->where('id = ?')
            ->andWhere('user_id = ?')
            ->setParameters([
                $id->value,
                $userId->value
            ])
            ->fetchAssociative();
        if ($rawItem === false) {
            return null;
        }

        return ProjectResponseDTO::create($rawItem);
    }

    /**
     * @param ProjectId $id
     * @return ProjectResponseDTO|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?ProjectResponseDTO
    {
        $rawItem = $this->queryBuilder()
            ->select('*')
            ->from('project_projections')
            ->where('id = ?')
            ->setParameters([
                $id->value
            ])
            ->fetchAssociative();
        if ($rawItem === false) {
            return null;
        }

        return ProjectResponseDTO::create($rawItem);
    }

    private function queryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::CONNECTION);
        return $connection->createQueryBuilder();
    }
}