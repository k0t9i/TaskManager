<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Finder;

interface StorageFinderInterface
{
    public function findAll(string $storageName): array;
}
