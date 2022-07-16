<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Collection\CollectionInterface;
use App\Shared\Infrastructure\Persistence\Hydrator\DTO\RehydratorCollectionDTO;
use App\Shared\Infrastructure\Persistence\Hydrator\DTO\RehydratorEntityDTO;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataField;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;

class Rehydrator
{
    private ?RehydratorEntityDTO $root = null;
    private array $added = [];
    private array $updated = [];
    private array $deleted = [];

    /**
     * @param AggregateRoot $aggregateRoot
     * @param StorageMetadataInterface $metadata
     * @return RehydratorEntityDTO[]
     */
    public function loadFromAggregateRoot(AggregateRoot $aggregateRoot, StorageMetadataInterface $metadata): RehydratorEntityDTO
    {
        $this->root = null;
        $this->added = [];
        $this->updated = [];
        $this->deleted = [];

        $this->root = $this->loadFromRecursive($aggregateRoot, $metadata);

        return $this->root;
    }

    private function loadFromRecursive(
        object $object,
        StorageMetadataInterface $metadata,
        ?object $parent = null
    ): RehydratorEntityDTO {
        $storageFields = $metadata->getStorageFields($parent);

        $result = [];

        foreach ($storageFields as $metadataField) {
            $propertyValue = $metadataField->valueAccessor->getValue($object);
            if ($propertyValue instanceof CollectionInterface) {
                $this->loadFromCollection($object, $metadataField);
            } elseif ($metadataField->metadata !== null) {
                $result = $this->loadFromRecursive($propertyValue, $metadataField->metadata, $object);
            } else {
                $result[$metadataField->name] = $propertyValue;
            }
        }

        return new RehydratorEntityDTO(
            $metadata->getStorageName(),
            $metadata->getPrimaryKey(),
            $result,
            $parent === null ? new RehydratorCollectionDTO($this->added, $this->updated, $this->deleted) : null
        );
    }

    private function loadFromCollection(object $parent, StorageMetadataField $metadataField): void
    {
        $collection = $metadataField->valueAccessor->getValue($parent);

        $added = $this->loadFromCollectionPartially($collection->getAdded(), $metadataField->metadata, $parent);
        $updated = $this->loadFromCollectionPartially($collection->getUpdated(), $metadataField->metadata, $parent);
        $deleted = $this->loadFromCollectionPartially($collection->getDeleted(), $metadataField->metadata, $parent);

        $this->added = array_merge($this->added, $added);
        $this->updated = array_merge($this->updated, $updated);
        $this->deleted = array_merge($this->deleted, $deleted);
    }

    private function loadFromCollectionPartially(
        array $items,
        StorageMetadataInterface $metadata,
        object $parent)
    : array {
        $result = [];

        foreach ($items as $key => $object) {
            $result[] = $this->loadFromRecursive($object, $metadata, $parent);
        }

        return $result;
    }
}
