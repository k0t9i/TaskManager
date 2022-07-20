<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Infrastructure\Persistence\Hydrator\Metadata\RequestManagerStorageMetadata;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Application\Storage\StorageLoaderInterface;
use App\Shared\Application\Storage\StorageSaverInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use App\Shared\Infrastructure\Service\CriteriaStorageFieldValidator;
use App\Shared\Infrastructure\Service\CriteriaToQueryBuilderConverter;
use App\Shared\Infrastructure\Service\OptimisticLockTrait;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class SqlRequestManagerRepository implements RequestManagerRepositoryInterface
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
     * @param ProjectId $id
     * @return RequestManager|null
     * @throws Exception
     */
    public function findByProjectId(ProjectId $id): ?RequestManager
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('projectId', '=', $id->value)
        ]));
    }

    /**
     * @param RequestId $id
     * @return RequestManager|null
     * @throws Exception
     */
    public function findByRequestId(RequestId $id): ?RequestManager
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('requests.id', '=', $id->value)
        ]));
    }

    public function findByCriteria(Criteria $criteria): ?RequestManager
    {
        /** @var RequestManager $result */
        [$result, $version] = $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata);
        if ($result !== null) {
            $this->setVersion($result->getId()->value, $version);
        }
        return $result;
    }

    /**
     * @param RequestManager $manager
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(RequestManager $manager): void
    {
        $prevVersion = $this->getVersion($manager->getId()->value);

        if ($prevVersion > 0) {
            $this->storageSaver->update($manager, $this->metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($manager, $this->metadata);
        }
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->managerRegistry->getConnection()->createQueryBuilder();
    }

    private function initMetadata(): void
    {
        $this->metadata = new RequestManagerStorageMetadata();
    }
}
