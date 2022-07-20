<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Infrastructure\Persistence\Hydrator\Metadata\ProjectStorageMetadata;
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

class SqlProjectRepository implements ProjectRepositoryInterface
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
     * @return Project|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?Project
    {
        return $this->findByCriteria(new Criteria([
            new ExpressionOperand('id', '=', $id->value)
        ]));
    }

    public function findByCriteria(Criteria $criteria): ?Project
    {
        /** @var Project $result */
        [$result, $version] = $this->findByCriteriaInternal($this->queryBuilder(), $criteria, $this->metadata);
        if ($result !== null) {
            $this->setVersion($result->getId()->value, $version);
        }
        return $result;
    }

    /**
     * @param Project $project
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(Project $project): void
    {
        $prevVersion = $this->getVersion($project->getId()->value);

        if ($prevVersion > 0) {
            $this->storageSaver->update($project, $this->metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($project, $this->metadata);
        }
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->managerRegistry->getConnection()->createQueryBuilder();
    }

    private function initMetadata(): void
    {
        $this->metadata = new ProjectStorageMetadata();
    }
}