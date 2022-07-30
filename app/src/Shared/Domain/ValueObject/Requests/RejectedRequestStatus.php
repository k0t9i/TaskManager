<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\Requests;

final class RejectedRequestStatus extends RequestStatus
{
    protected function getNextStatuses(): array
    {
        return [];
    }
}
