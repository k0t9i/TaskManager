<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence;

use App\Shared\Infrastructure\Exception\OptimisticLockException;

trait OptimisticLockTrait
{
    private array $versions = [];

    private function saveVersion(string $entityId, int $version): void
    {
        $this->versions[$entityId] = $version;
    }

    /**
     * @param string $entityId
     * @param int $version
     * @throws OptimisticLockException
     */
    private function ensureIsVersionLesserThanPrevious(string $entityId, int $version): void
    {
        if ($version > 0) {
            $prevVersion = $this->versions[$entityId] ?? -1;
            if ($version >= $prevVersion + 1) {
                throw new OptimisticLockException($entityId, $version, $prevVersion);
            }
        }
        unset($this->versions[$entityId]);
    }
}
