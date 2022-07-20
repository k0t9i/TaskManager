<?php
declare(strict_types=1);

namespace App\Shared\Application\Storage;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;

interface StorageLoaderInterface
{
    public function load(StorageFinderInterface $finder, StorageMetadataInterface $metadata): array;
    public function loadAll(StorageFinderInterface $finder, StorageMetadataInterface $metadata): array;
}
