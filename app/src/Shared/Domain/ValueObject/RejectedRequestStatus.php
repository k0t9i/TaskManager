<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final class RejectedRequestStatus extends RequestStatus
{
    protected function getNextStatuses(): array
    {
        return [];
    }
}
