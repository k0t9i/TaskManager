<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Security\AuthUserInterface;

final class AuthUser implements AuthUserInterface
{
    public function __construct(private readonly string $id)
    {
    }

    public function getId(): UserId
    {
        return new UserId($this->id);
    }
}
