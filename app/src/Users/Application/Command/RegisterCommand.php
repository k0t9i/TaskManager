<?php

declare(strict_types=1);

namespace App\Users\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class RegisterCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $password,
        public readonly string $repeatPassword,
    ) {
    }

    public static function createFromRequest(string $id, array $item): self
    {
        return new self(
            $id,
            $item['email'] ?? '',
            $item['firstname'] ?? '',
            $item['lastname'] ?? '',
            $item['password'] ?? '',
            $item['repeat_password'] ?? '',
        );
    }
}
