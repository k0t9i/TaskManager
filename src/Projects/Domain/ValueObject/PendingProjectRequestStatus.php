<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

final class PendingProjectRequestStatus extends ProjectRequestStatus
{
    protected function getNextStatuses(): array
    {
        return [
            RejectedProjectRequestStatus::class,
            ConfirmedProjectRequestStatus::class
        ];
    }
}
