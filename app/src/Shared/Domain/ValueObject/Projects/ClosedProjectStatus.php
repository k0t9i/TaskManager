<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\Projects;

final class ClosedProjectStatus extends ProjectStatus
{
    public function allowsModification(): bool
    {
        return false;
    }

    protected function getNextStatuses(): array
    {
        return [ActiveProjectStatus::class];
    }
}