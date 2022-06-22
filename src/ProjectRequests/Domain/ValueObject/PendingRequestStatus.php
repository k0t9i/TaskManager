<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\ValueObject;

final class PendingRequestStatus extends RequestStatus
{
    protected function getNextStatuses(): array
    {
        return [
            RejectedRequestStatus::class,
            ConfirmedRequestStatus::class
        ];
    }
}
