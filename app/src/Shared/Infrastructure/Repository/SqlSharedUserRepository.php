<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Application\Storage\StorageLoaderInterface;
use App\Shared\Application\Storage\StorageSaverInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Entity\SharedUser;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\SharedUserStorageMetadata;
use App\Shared\Infrastructure\Service\CriteriaStorageFieldValidator;
use App\Shared\Infrastructure\Service\CriteriaToQueryBuilderConverter;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class SqlSharedUserRepository implements SharedUserRepositoryInterface
{
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
     * @return SharedUser|null
     * @throws Exception
     */
    public function findById(UserId $id): ?SharedUser
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('id', '=', $id->value)
        ]));
    }

    public function findByCriteria(Criteria $criteria): ?SharedUser
    {
        /** @var SharedUser $result */
        [$result] = $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata);
        return $result;
    }

    /**
     * @param SharedUser $user
     * @throws Exception
     */
    public function save(SharedUser $user): void
    {
        if ($this->findById($user->getId()) !== null) {
            $this->storageSaver->update($user, $this->metadata);
        } else {
            $this->storageSaver->insert($user, $this->metadata, false);
        }
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->managerRegistry->getConnection()->createQueryBuilder();
    }

    private function initMetadata(): void
    {
        $this->metadata = new SharedUserStorageMetadata();
    }
}