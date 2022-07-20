<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use App\Users\Domain\DTO\ProfileResponseDTO;
use App\Users\Domain\DTO\UserResponseDTO;
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
     * @return UserResponseDTO[]
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

    public function findByCriteria(Criteria $criteria): ?UserResponseDTO
    {
        return $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->userMetadata);
    }

    /**
     * @throws Exception
     */
    public function findProfileByCriteria(Criteria $criteria): ?ProfileResponseDTO
    {
        return $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->profileMetadata);
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