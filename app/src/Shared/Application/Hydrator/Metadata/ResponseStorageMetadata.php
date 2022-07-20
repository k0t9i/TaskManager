<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;

abstract class ResponseStorageMetadata extends StorageMetadata
{
    protected const COLUMN_TO_PROPERTY_MAP = [];

    private ?array $storageFields = null;

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        if ($this->storageFields === null) {
            $this->storageFields = [];
            foreach (static::COLUMN_TO_PROPERTY_MAP as $column => $property) {
                $this->storageFields[$property] = new StorageMetadataField(
                    $column,
                    new PropertyValueAccessor($property),
                    new PropertyValueMutator($property)
                );
            }
        }
        return $this->storageFields;
    }
}
