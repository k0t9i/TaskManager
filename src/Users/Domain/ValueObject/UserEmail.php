<?php
declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Email;

final class UserEmail extends Email
{
    protected function ensureIsValid(): void
    {
        $attributeName = 'User email';
        $this->ensureNotEmpty($attributeName);
        $this->ensureValidEmail($attributeName);
    }
}