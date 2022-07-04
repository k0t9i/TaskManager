<?php
declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

use App\Shared\Domain\ValueObject\StringValueObject;

final class TaskDescription extends StringValueObject
{
    private const MAX_LENGTH = 4000;

    protected function ensureIsValid(): void
    {
        $this->ensureValidMaxLength('Task description', self::MAX_LENGTH);
    }
}