<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

trait OptimisticLockTrait
{
    private array $versions = [];

    private function setVersion(string $entityId, int $version): void
    {
        $this->versions[$entityId] = $version;
    }

    private function getVersion(string $entityId): int
    {
        return $this->versions[$entityId] ?? -1;
    }
}
