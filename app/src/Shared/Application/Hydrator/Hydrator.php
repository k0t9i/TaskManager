<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator;

use App\Shared\Application\Hydrator\DTO\HydratorCollectionDTO;
use App\Shared\Application\Hydrator\DTO\HydratorEntityDTO;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Collection\CollectionInterface;
use App\Shared\Domain\Collection\Hashable;
use ReflectionClass;
use ReflectionException;

final class Hydrator implements HydratorInterface
{
    /**
     * @throws ReflectionException
     */
    public function loadIntoEntity(
        StorageMetadataInterface $metadata,
        HydratorEntityDTO $data
    ): object {
        return $this->loadRecursive($metadata, $data);
    }

    /**
     * @param StorageMetadataInterface $metadata
     * @param HydratorEntityDTO $data
     * @return object
     * @throws ReflectionException
     */
    private function loadRecursive(
        StorageMetadataInterface $metadata,
        HydratorEntityDTO $data
    ): object {
        $storageFields = $metadata->getStorageFields();
        $reflection = new ReflectionClass($metadata->getClassName());
        $object = $reflection->newInstanceWithoutConstructor();

        foreach ($storageFields as $metadataField) {
            if ($metadataField->valueMutator === null) {
                continue;
            }

            $collection = $metadataField->valueMutator->getOrCreateObject($object);
            if ($collection instanceof CollectionInterface) {
                $metadataField->valueMutator->setValue(
                    $object,
                    $this->loadIntoCollection($collection, $metadataField->metadata, $data->children)
                );
            } else {
                $item = $data->columns[$metadataField->name];
                $metadataField->valueMutator->setValue($object, $item);
            }
        }

        return $object;
    }

    /**
     * @param CollectionInterface $collection
     * @param StorageMetadataInterface $metadata
     * @param HydratorCollectionDTO $collectionData
     * @return CollectionInterface
     * @throws ReflectionException
     */
    private function loadIntoCollection(
        CollectionInterface $collection,
        StorageMetadataInterface $metadata,
        HydratorCollectionDTO $collectionData
    ): CollectionInterface {
        $storageName = $metadata->getStorageName();
        $items = $collectionData->getByTable($storageName);
        if (count($items) > 0) {
            foreach ($items as $item) {
                /** @var Hashable $collectionItem */
                $collectionItem = $this->loadRecursive(
                    $metadata,
                    $item
                );
                $collection = $collection->add($collectionItem);
            }
            $collection->flush();
        }
        return $collection;
    }
}
