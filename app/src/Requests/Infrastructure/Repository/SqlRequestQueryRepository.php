<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Repository\RequestQueryRepositoryInterface;
use App\Requests\Infrastructure\Persistence\Hydrator\Metadata\RequestListProjectionStorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class SqlRequestQueryRepository implements RequestQueryRepositoryInterface
{
    use SqlCriteriaRepositoryTrait;

    private const CONNECTION = 'read';

    private readonly StorageMetadataInterface $metadata;

    public function findAllByCriteria(Criteria $criteria): array
    {
        return $this->findAllByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata);
    }

    /**
     * @param Criteria $criteria
     * @return int
     * @throws Exception
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        return $this->findCountByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata);
    }

    private function queryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::CONNECTION);
        return $connection->createQueryBuilder();
    }

    private function initMetadata(): void
    {
        $this->metadata = new RequestListProjectionStorageMetadata();
    }
}