<?php
declare(strict_types=1);

namespace App\Users\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class RegisterCommand implements CommandInterface
{
    public function __construct(
        public readonly string $email,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $password,
        public readonly string $repeatPassword,
    ) {
    }

    public static function createFromRequest(array $item): self
    {
        return new self(
            $item['email'] ?? '',
            $item['firstname'] ?? '',
            $item['lastname'] ?? '',
            $item['password'] ?? '',
            $item['repeat_password'] ?? '',
        );
    }
}
