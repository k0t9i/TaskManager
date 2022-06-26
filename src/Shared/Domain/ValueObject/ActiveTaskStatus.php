<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final class ActiveTaskStatus extends TaskStatus
{
    public function allowsModification(): bool
    {
        return true;
    }

    protected function getNextStatuses(): array
    {
        return [ClosedTaskStatus::class];
    }
}