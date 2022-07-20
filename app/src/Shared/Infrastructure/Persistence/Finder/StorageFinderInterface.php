<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Finder;

interface StorageFinderInterface
{
    public function find(string $storageName): array;
    public function findAll(string $storageName): array;
}
