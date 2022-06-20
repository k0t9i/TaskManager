<?php
declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

final class UserEmail
{
    public function __construct(public readonly string $value)
    {
    }
}