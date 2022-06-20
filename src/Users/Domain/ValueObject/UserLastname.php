<?php
declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

class UserLastname
{
    public function __construct(public readonly string $value)
    {
    }
}