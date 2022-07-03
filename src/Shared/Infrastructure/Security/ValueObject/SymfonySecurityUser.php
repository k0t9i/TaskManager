<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Security\ValueObject;

use Symfony\Component\Security\Core\User\UserInterface;

final class SymfonySecurityUser implements UserInterface
{
    public function __construct(private readonly string $userIdentifier)
    {
    }

    public function getRoles(): array
    {
        return [];
    }

    public function eraseCredentials()
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
}
