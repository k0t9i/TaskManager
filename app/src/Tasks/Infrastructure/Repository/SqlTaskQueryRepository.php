<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use App\Tasks\Domain\DTO\TaskResponseDTO;
use App\Tasks\Domain\Repository\TaskQueryRepositoryInterface;
use App\Tasks\Infrastructure\Persistence\Hydrator\Metadata\TaskListResponseStorageMetadata;
use App\Tasks\Infrastructure\Persistence\Hydrator\Metadata\TaskResponseStorageMetadata;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class SqlTaskQueryRepository implements TaskQueryRepositoryInterface
{
    use SqlCriteriaRepositoryTrait;

    private const CONNECTION = 'read';

    private readonly StorageMetadataInterface $listMetadata;
    private readonly StorageMetadataInterface $metadata;

    public function findAllByCriteria(Criteria $criteria): array
    {
        return $this->findAllByCriteriaInternal($this->queryBuilder(), $criteria, $this->listMetadata);
    }

    /**
     * @param Criteria $criteria
     * @return int
     * @throws Exception
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        return $this->findCountByCriteriaInternal($this->queryBuilder(), $criteria, $this->listMetadata);
    }

    public function findByCriteria(Criteria $criteria): ?TaskResponseDTO
    {
        return $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata)[0];
    }

    private function queryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::CONNECTION);
        return $connection->createQueryBuilder();
    }

    private function initMetadata(): void
    {
        $this->listMetadata = new TaskListResponseStorageMetadata();
        $this->metadata = new TaskResponseStorageMetadata();
    }
}