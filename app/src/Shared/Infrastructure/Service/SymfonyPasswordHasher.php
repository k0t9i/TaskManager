<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Service\PasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as SymfonyPasswordHasherInterface;

final class SymfonyPasswordHasher implements PasswordHasherInterface
{
    public function __construct(private readonly SymfonyPasswordHasherInterface $hasher)
    {
    }

    public function hashPassword(string $plainPassword): string
    {
        return $this->hasher->hash($plainPassword);
    }

    public function verifyPassword(string $hashedPassword, string $plainPassword): bool
    {
        return $this->hasher->verify($hashedPassword, $plainPassword);
    }
}
