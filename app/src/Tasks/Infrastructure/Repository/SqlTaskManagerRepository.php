<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Application\Storage\StorageLoaderInterface;
use App\Shared\Application\Storage\StorageSaverInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use App\Shared\Infrastructure\Service\CriteriaStorageFieldValidator;
use App\Shared\Infrastructure\Service\CriteriaToQueryBuilderConverter;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Infrastructure\Persistence\Hydrator\Metadata\TaskManagerStorageMetadata;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

final class SqlTaskManagerRepository implements TaskManagerRepositoryInterface
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
     * @return TaskManager|null
     * @throws Exception
     */
    public function findByProjectId(ProjectId $id): ?TaskManager
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('projectId', '=', $id->value)
        ]));
    }

    /**
     * @param TaskId $id
     * @return TaskManager|null
     * @throws Exception
     */
    public function findByTaskId(TaskId $id): ?TaskManager
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('tasks.id', '=', $id->value)
        ]));
    }

    public function findByCriteria(Criteria $criteria): ?TaskManager
    {
        /** @var TaskManager $result */
        [$result, $version] = $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata);
        if ($result !== null) {
            $this->setVersion($result->getId()->value, $version);
        }
        return $result;
    }

    /**
     * @param TaskManager $manager
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(TaskManager $manager): void
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
        $this->metadata = new TaskManagerStorageMetadata();
    }
}
