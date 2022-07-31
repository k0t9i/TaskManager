<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Symfony\DTO;

use App\Users\Application\Command\RegisterCommand;

final class UserRegisterDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $password,
        public readonly string $repeatPassword,
    ) {
    }

    public function createCommand(string $id): RegisterCommand
    {
        return new RegisterCommand(
            $id,
            $this->email,
            $this->firstname,
            $this->lastname,
            $this->password,
            $this->repeatPassword,
        );
    }
}
