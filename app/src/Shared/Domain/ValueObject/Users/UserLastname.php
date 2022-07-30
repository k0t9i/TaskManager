<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\Users;

use App\Shared\Domain\ValueObject\StringValueObject;

final class UserLastname extends StringValueObject
{
    private const MAX_LENGTH = 255;

    protected function ensureIsValid(): void
    {
        $attributeName = 'User lastname';
        $this->ensureNotEmpty($attributeName);
        $this->ensureValidMaxLength($attributeName, self::MAX_LENGTH);
    }
}
