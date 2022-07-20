<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

abstract class StorageMetadata implements StorageMetadataInterface
{
    private ?array $pk = null;
    private ?array $propertyToColumnMap = null;

    public function getPrimaryKey(): array
    {
        if ($this->pk === null) {
            $this->pk = [];
            foreach ($this->getStorageFields() as $metadataField) {
                if ($metadataField->isPrimaryKey) {
                    $this->pk[] = $metadataField->name;
                }
            }
        }
        return $this->pk;
    }

    public function getPropertyToColumnMap(): array
    {
        if ($this->propertyToColumnMap === null) {
            $this->propertyToColumnMap = [];
            foreach ($this->getStorageFields() as $propertyName => $metadataField) {
                if (!is_int($propertyName) && $metadataField->metadata === null) {
                    $this->propertyToColumnMap[$propertyName] = $metadataField->name;
                }
            }
        }
        return $this->propertyToColumnMap;
    }
}
