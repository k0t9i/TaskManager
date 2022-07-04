<?php
declare(strict_types=1);

namespace App\Shared\Domain\Security;

interface PasswordHasherInterface
{
    public function hashPassword(string $plainPassword): string;
    public function verifyPassword(string $hashedPassword, string $plainPassword): bool;
}
