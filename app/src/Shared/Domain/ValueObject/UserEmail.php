<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final class UserEmail extends Email
{
    protected function ensureIsValid(): void
    {
        $attributeName = 'User email';
        $this->ensureNotEmpty($attributeName);
        $this->ensureValidEmail($attributeName);
    }
}