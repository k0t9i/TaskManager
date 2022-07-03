<?php
declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

use App\Shared\Domain\ValueObject\StringValueObject;

final class UserPassword extends StringValueObject
{
    protected function ensureIsValid(): void
    {
    }
}