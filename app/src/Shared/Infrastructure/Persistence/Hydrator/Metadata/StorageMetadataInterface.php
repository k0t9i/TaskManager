<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Metadata;

interface StorageMetadataInterface
{
    public function getClassName(): string;
    public function getStorageName(): string;
    public function getPrimaryKey(): array;

    /**
     * @return StorageMetadataField[]
     */
    public function getStorageFields(?object $parentObject = null): array;
}
