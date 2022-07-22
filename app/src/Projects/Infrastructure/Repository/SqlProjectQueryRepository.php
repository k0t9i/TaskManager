<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Entity\ProjectListProjection;
use App\Projects\Domain\Entity\ProjectProjection;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Projects\Infrastructure\Persistence\Hydrator\Metadata\ProjectListProjectionStorageMetadata;
use App\Projects\Infrastructure\Persistence\Hydrator\Metadata\ProjectProjectionStorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class SqlProjectQueryRepository implements ProjectQueryRepositoryInterface
{
    use SqlCriteriaRepositoryTrait;

    private const CONNECTION = 'read';

    private readonly StorageMetadataInterface $listMetadata;
    private readonly StorageMetadataInterface $metadata;

    /**
     * @param Criteria $criteria
     * @return ProjectListProjection[]
     * @throws Exception
     */
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

    public function findByCriteria(Criteria $criteria): ?ProjectProjection
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
        $this->listMetadata = new ProjectListProjectionStorageMetadata();
        $this->metadata = new ProjectProjectionStorageMetadata();
    }
}