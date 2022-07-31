<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Symfony\DTO;

use App\Users\Application\Command\UpdateProfileCommand;

final class ProfileUpdateDTO
{
    public function __construct(
        public readonly ?string $firstname,
        public readonly ?string $lastname,
        public readonly ?string $password,
        public readonly ?string $repeatPassword
    ) {
    }

    public function createCommand(): UpdateProfileCommand
    {
        return new UpdateProfileCommand(
            $this->firstname,
            $this->lastname,
            $this->password,
            $this->repeatPassword
        );
    }
}
