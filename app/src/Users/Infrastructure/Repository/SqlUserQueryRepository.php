<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use App\Users\Domain\Entity\ProfileProjection;
use App\Users\Domain\Entity\UserProjection;
use App\Users\Domain\Repository\UserQueryRepositoryInterface;
use App\Users\Infrastructure\Persistence\Hydrator\Metadata\ProfileResponseStorageMetadata;
use App\Users\Infrastructure\Persistence\Hydrator\Metadata\UserResponseStorageMetadata;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

class SqlUserQueryRepository implements UserQueryRepositoryInterface
{
    use SqlCriteriaRepositoryTrait;

    private const CONNECTION = 'read';

    private readonly StorageMetadataInterface $userMetadata;
    private readonly StorageMetadataInterface $profileMetadata;

    /**
     * @param Criteria $criteria
     * @return UserProjection[]
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        return $this->findAllByCriteriaInternal($this->queryBuilder(), $criteria, $this->userMetadata);
    }

    /**
     * @param Criteria $criteria
     * @return int
     * @throws Exception
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        return $this->findCountByCriteriaInternal($this->queryBuilder(), $criteria, $this->userMetadata);
    }

    public function findByCriteria(Criteria $criteria): ?UserProjection
    {
        return $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->userMetadata)[0];
    }

    /**
     * @throws Exception
     */
    public function findProfileByCriteria(Criteria $criteria): ?ProfileProjection
    {
        return $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->profileMetadata)[0];
    }

    private function queryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::CONNECTION);
        return $connection->createQueryBuilder();
    }

    private function initMetadata(): void
    {
        $this->userMetadata = new UserResponseStorageMetadata();
        $this->profileMetadata = new ProfileResponseStorageMetadata();
    }
}