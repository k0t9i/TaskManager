<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Factory\RequestStatusFactory;

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

    public function whetherToAddUser(): bool
    {
        return false;
    }
}
