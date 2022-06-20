<?php
declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

class UserFirstname
{
    public function __construct(public readonly string $value)
    {
    }
}