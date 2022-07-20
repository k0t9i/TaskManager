<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Storage;

use App\Shared\Application\Hydrator\DTO\HydratorCollectionDTO;
use App\Shared\Application\Hydrator\DTO\HydratorEntityDTO;
use App\Shared\Application\Hydrator\HydratorInterface;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Application\Storage\StorageFinderInterface;
use App\Shared\Application\Storage\StorageLoaderInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

final class SqlStorageLoader implements StorageLoaderInterface
{
    public function __construct(
        private readonly HydratorInterface $hydrator,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param StorageFinderInterface $finder
     * @param StorageMetadataInterface $metadata
     * @return array
     * @throws Exception
     */
    public function load(StorageFinderInterface $finder, StorageMetadataInterface $metadata): array
    {
        $raw = $finder->find($metadata->getStorageName());

        if (count($raw) === 0) {
            return [null, -1];
        }

        $dto = $this->loadChildren($metadata, $raw);
        $version = $raw['version'] ?? 0;
        return [$this->hydrator->loadIntoEntity($metadata, $dto), $version];
    }

    /**
     * @param StorageFinderInterface $finder
     * @param StorageMetadataInterface $metadata
     * @return array
     * @throws Exception
     */
    public function loadAll(StorageFinderInterface $finder, StorageMetadataInterface $metadata): array
    {
        $raw = $finder->findAll($metadata->getStorageName());

        $result = [];
        foreach ($raw as $item) {
            $dto = $this->loadChildren($metadata, $item);
            $version = $item['version'] ?? 0;

            $result[] = [$this->hydrator->loadIntoEntity($metadata, $dto), $version];
        }

        return $result;
    }

    /**
     * @param StorageMetadataInterface $metadata
     * @param array $parentData
     * @return HydratorEntityDTO
     * @throws Exception
     */
    private function loadChildren(StorageMetadataInterface $metadata, array $parentData): HydratorEntityDTO
    {
        $parentPk = $this->fillParentPk($metadata, $parentData);

        $items = [];
        foreach ($metadata->getStorageFields() as $metadataField) {
            if ($metadataField->metadata !== null) {
                $queryBuilder = $this->buildConditions($metadataField->metadata, $parentPk);
                $raw = $queryBuilder->fetchAllAssociative();
                foreach ($raw as $rawItem) {
                    $item = $this->loadChildren($metadataField->metadata, $rawItem);
                    $items[] = $item;
                }
            }
        }

        return new HydratorEntityDTO(
            $metadata->getStorageName(),
            $parentData,
            new HydratorCollectionDTO($items)
        );
    }

    private function fillParentPk(StorageMetadataInterface $metadata, array $parentData): array
    {
        $parentPk = [];
        foreach ($metadata->getPrimaryKey() as $column) {
            $parentPk[$column] = $parentData[$column];
        }
        return $parentPk;
    }

    private function buildConditions(StorageMetadataInterface $metadata, array $parentPk): QueryBuilder
    {
        $builder = $this->entityManager
            ->getConnection()
            ->createQueryBuilder()
            ->select('*')
            ->from($metadata->getStorageName());

        $conditions = [];
        foreach ($metadata->getStorageFields() as $childField) {
            if ($childField->parentColumn !== null) {
                $conditions[$childField->name] = $parentPk[$childField->parentColumn];
            }
        }

        foreach ($conditions as $column => $value) {
            $builder->andWhere($column . ' = :' . $column)
                ->setParameter($column, $value);
        }

        return $builder;
    }
}
