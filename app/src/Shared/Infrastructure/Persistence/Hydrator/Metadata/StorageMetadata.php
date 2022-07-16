<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

abstract class StorageMetadata implements StorageMetadataInterface
{
    private ?array $pk = null;

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
}
