<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Status;

abstract class RequestStatus extends Status
{
    public function allowsModification(): bool
    {
        return true;
    }
}
