<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

final class ConfirmedProjectRequestStatus extends ProjectRequestStatus
{
    protected function getNextStatuses(): array
    {
        return [];
    }
}
