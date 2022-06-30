<?php
declare(strict_types=1);

namespace App\Requests\Domain\ValueObject;

use App\Requests\Domain\Factory\RequestStatusFactory;
use App\Shared\Domain\ValueObject\Status;

abstract class RequestStatus extends Status
{
    public function allowsModification(): bool
    {
        return true;
    }

    public function getScalar(): int
    {
        return RequestStatusFactory::scalarFromObject($this);
    }
}
