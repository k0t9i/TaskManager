<?php

declare(strict_types=1);

namespace App\Users\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class UpdateProfileCommand implements CommandInterface
{
    public function __construct(
        public readonly ?string $firstname,
        public readonly ?string $lastname,
        public readonly ?string $password,
        public readonly ?string $repeatPassword
    ) {
    }
}
