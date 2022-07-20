<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Finder\SqlStorageFinder;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Persistence\StorageLoaderInterface;
use App\Shared\Infrastructure\Service\CriteriaStorageFieldValidator;
use App\Shared\Infrastructure\Service\CriteriaToQueryBuilderConverter;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

trait SqlCriteriaRepositoryTrait
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly StorageLoaderInterface $storageLoader,
        private readonly CriteriaToQueryBuilderConverter $criteriaConverter,
        private readonly CriteriaStorageFieldValidator $criteriaValidator
    ) {
        $this->initMetadata();
    }

    abstract private function initMetadata(): void;

    private function findAllByCriteriaInternal(
        QueryBuilder $builder,
        Criteria $criteria,
        StorageMetadataInterface $metadata
    ): array {
        $builder->select($metadata->getStorageName() . '.*');
        $this->criteriaValidator->validate($criteria, $metadata);
        $this->criteriaConverter->convert($builder, $criteria, $metadata);

        $rawItems = $this->storageLoader->loadAll(new SqlStorageFinder($builder), $metadata);
        $result = [];
        foreach ($rawItems as [$item, $version]) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    private function findCountByCriteriaInternal(
        QueryBuilder $builder,
        Criteria $criteria,
        StorageMetadataInterface $metadata
    ): int {
        $builder->select('count(*)')
            ->from($metadata->getStorageName());
        $this->criteriaValidator->validate($criteria, $metadata);
        $this->criteriaConverter->convert($builder, $criteria, $metadata);

        $builder->setFirstResult(0);
        $builder->setMaxResults(null);

        return $builder->fetchOne();
    }

    private function findByCriteriaInternal(
        QueryBuilder $builder,
        Criteria $criteria,
        StorageMetadataInterface $metadata
    ): array {
        $builder = $builder->select($metadata->getStorageName() . '.*');
        $this->criteriaValidator->validate($criteria, $metadata);
        $this->criteriaConverter->convert($builder, $criteria, $metadata);

        return $this->storageLoader->load(new SqlStorageFinder($builder), $metadata);
    }
}
