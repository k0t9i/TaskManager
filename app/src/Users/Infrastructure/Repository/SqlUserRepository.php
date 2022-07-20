<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Application\Storage\StorageLoaderInterface;
use App\Shared\Application\Storage\StorageSaverInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use App\Shared\Infrastructure\Service\CriteriaStorageFieldValidator;
use App\Shared\Infrastructure\Service\CriteriaToQueryBuilderConverter;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Infrastructure\Persistence\Hydrator\Metadata\UserStorageMetadata;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class SqlUserRepository implements UserRepositoryInterface
{
    use OptimisticLockTrait;
    use SqlCriteriaRepositoryTrait{
        SqlCriteriaRepositoryTrait::__construct as private traitConstruct;
    }

    private readonly StorageMetadataInterface $metadata;

    public function __construct(
        private readonly StorageSaverInterface $storageSaver,
        ManagerRegistry $managerRegistry,
        StorageLoaderInterface $storageLoader,
        CriteriaToQueryBuilderConverter $criteriaConverter,
        CriteriaStorageFieldValidator $criteriaValidator
    ) {
        $this->traitConstruct($managerRegistry, $storageLoader, $criteriaConverter, $criteriaValidator);
    }

    /**
     * @param UserId $id
     * @return User|null
     * @throws Exception
     */
    public function findById(UserId $id): ?User
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('id', '=', $id->value)
        ]));
    }

    /**
     * @param UserEmail $email
     * @return User|null
     * @throws Exception
     */
    public function findByEmail(UserEmail $email): ?User
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('email', '=', $email->value)
        ]));
    }

    public function findByCriteria(Criteria $criteria): ?User
    {
        /** @var User $result */
        [$result, $version] = $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata);
        if ($result !== null) {
            $this->setVersion($result->getId()->value, $version);
        }
        return $result;
    }

    /**
     * @param User $user
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        $prevVersion = $this->getVersion($user->getId()->value);

        if ($prevVersion > 0) {
            $this->storageSaver->update($user, $this->metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($user, $this->metadata);
        }
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->managerRegistry->getConnection()->createQueryBuilder();
    }

    private function initMetadata(): void
    {
        $this->metadata = new UserStorageMetadata();
    }
}