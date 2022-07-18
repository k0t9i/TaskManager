<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\Hydrator\DTO\RehydratorCollectionDTO;
use App\Shared\Infrastructure\Persistence\Hydrator\DTO\RehydratorEntityDTO;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Persistence\Hydrator\RehydratorInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

final class SqlStorageSaver implements StorageSaverInterface
{
    public function __construct(
        private readonly RehydratorInterface $rehydrator,
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param AggregateRoot $object
     * @param StorageMetadataInterface $metadata
     * @param bool $isVersioned
     * @throws Exception
     */
    public function insert(AggregateRoot $object, StorageMetadataInterface $metadata, bool $isVersioned = true): void
    {
        $dto = $this->rehydrator->loadFromAggregateRoot($object, $metadata);

        $this->innerInsert($dto, $isVersioned ? 1 : null);
    }

    /**
     * @param AggregateRoot $object
     * @param StorageMetadataInterface $metadata
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function update(AggregateRoot $object, StorageMetadataInterface $metadata, ?int $prevVersion = null): void
    {
        $dto = $this->rehydrator->loadFromAggregateRoot($object, $metadata);
        $newVersion = null;
        if ($prevVersion !== null) {
            $version = $this->getVersion($dto);
            if ($version > $prevVersion) {
                throw new OptimisticLockException($version, $prevVersion);
            }
            $newVersion = $prevVersion + 1;
        }

        if ($dto->children !== null) {
            $this->insertCollection($dto->children);
            $this->updateCollection($dto->children);
            $this->deleteCollection($dto->children);
            $this->flushCollections($dto->children);
        }
        $this->innerUpdate($dto, $newVersion);
    }

    /**
     * @param AggregateRoot $object
     * @param StorageMetadataInterface $metadata
     * @return int
     * @throws Exception
     */
    private function getVersion(RehydratorEntityDTO $dto): int
    {
        $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

        $queryBuilder->select('version')
            ->from($dto->table);

        foreach ($dto->primaryKey as $item) {
            $queryBuilder->andWhere($item . ' = :' . $item)
                ->setParameter($item, $dto->columns[$item]);
        }

        $version = $queryBuilder->fetchOne();

        return $version ?: 0;
    }

    /**
     * @param RehydratorCollectionDTO $dto
     * @throws Exception
     */
    private function insertCollection(RehydratorCollectionDTO $dto): void
    {
        foreach ($dto->added as $childDto) {
            $this->innerInsert($childDto);
        }
    }

    /**
     * @param RehydratorCollectionDTO $dto
     * @throws Exception
     */
    private function updateCollection(RehydratorCollectionDTO $dto): void
    {
        foreach ($dto->updated as $childDto) {
            $this->innerUpdate($childDto);
        }
    }

    /**
     * @param RehydratorCollectionDTO $dto
     * @throws Exception
     */
    private function deleteCollection(RehydratorCollectionDTO $dto): void
    {
        foreach ($dto->deleted as $childDto) {
            $this->innerDelete($childDto);
        }
    }

    private function flushCollections(RehydratorCollectionDTO $dto): void
    {
        foreach ($dto->originalCollections as $collection) {
            $collection->flush();
        }
    }

    /**
     * @throws Exception
     */
    private function innerUpdate(RehydratorEntityDTO $dto, ?int $version = null): void
    {
        $columns = $dto->columns;

        if (count($columns) > 0) {
            $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

            foreach ($dto->primaryKey as $item) {
                $queryBuilder->andWhere($item . ' = :' . $item)
                    ->setParameter($item, $columns[$item]);
                unset($columns[$item]);
            }

            $queryBuilder->update($dto->table);
            foreach ($columns as $name => $value) {
                $queryBuilder->set($name, ':' . $name)
                    ->setParameter($name, $value);
            }
            if ($version !== null) {
                $columnName = 'version';
                $queryBuilder->set($columnName, ':' . $columnName)
                    ->setParameter($columnName, $version);
            }

            $queryBuilder->executeStatement();
        }
    }

    /**
     * @throws Exception
     */
    private function innerInsert(RehydratorEntityDTO $dto, ?int $version = null): void
    {
        $columns = $dto->columns;

        if (count($columns) > 0) {
            $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

            $queryBuilder->insert($dto->table);
            foreach ($columns as $name => $value) {
                $queryBuilder->setValue($name, ':' . $name)
                    ->setParameter($name, $value);
            }

            if ($version !== null) {
                $columnName = 'version';
                $queryBuilder->setValue($columnName, ':' . $columnName)
                    ->setParameter($columnName, $version);
            }

            $queryBuilder->executeStatement();
        }
    }

    /**
     * @param RehydratorEntityDTO $dto
     * @throws Exception
     */
    private function innerDelete(RehydratorEntityDTO $dto): void
    {
        $columns = $dto->columns;

        if (count($columns) > 0) {
            $queryBuilder = $this->entityManager->getConnection()->createQueryBuilder();

            foreach ($dto->primaryKey as $item) {
                $queryBuilder->andWhere($item . ' = :' . $item)
                    ->setParameter($item, $columns[$item]);
                unset($columns[$item]);
            }

            $queryBuilder->delete($dto->table);

            $queryBuilder->executeStatement();
        }
    }
}
