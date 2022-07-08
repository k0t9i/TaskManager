<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final class ConfirmedRequestStatus extends RequestStatus
{
    protected function getNextStatuses(): array
    {
        return [];
    }

    public function whetherToAddUser(): bool
    {
        return true;
    }
}
