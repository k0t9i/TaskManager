<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

final class RejectedProjectRequestStatus extends ProjectRequestStatus
{
    protected function getNextStatuses(): array
    {
        return [];
    }
}
