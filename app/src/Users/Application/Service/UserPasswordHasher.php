<?php
declare(strict_types=1);

namespace App\Users\Application\Service;

use App\Shared\Application\Service\PasswordHasherInterface;
use App\Users\Domain\ValueObject\UserPassword;

final class UserPasswordHasher
{
    public function __construct(private readonly PasswordHasherInterface $hasher)
    {
    }

    public function hash(UserPassword $password): UserPassword
    {
        return new UserPassword($this->hasher->hashPassword($password->value));
    }

    public function verify(UserPassword $hashedPassword, UserPassword $plainPassword): bool
    {
        return $this->hasher->verifyPassword($hashedPassword->value, $plainPassword->value);
    }
}
