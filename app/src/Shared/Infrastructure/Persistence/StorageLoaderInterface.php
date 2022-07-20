<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Infrastructure\Persistence\Finder\StorageFinderInterface;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;

interface StorageLoaderInterface
{
    public function load(StorageFinderInterface $finder, StorageMetadataInterface $metadata): array;
    public function loadAll(StorageFinderInterface $finder, StorageMetadataInterface $metadata): array;
}
