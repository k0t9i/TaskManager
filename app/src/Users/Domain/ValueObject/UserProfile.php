<?php

declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Users\UserFirstname;
use App\Shared\Domain\ValueObject\Users\UserLastname;
use App\Users\Domain\Exception\PasswordAndRepeatPasswordDoNotMatchException;

final class UserProfile
{
    public function __construct(
        public readonly UserFirstname $firstname,
        public readonly UserLastname $lastname,
        public readonly UserPassword $password,
        public readonly ?UserPassword $repeatPassword = null
    ) {
        $this->ensureDoPasswordsMatch();
    }

    private function ensureDoPasswordsMatch(): void
    {
        if (null === $this->repeatPassword) {
            return;
        }
        if ($this->password->value !== $this->repeatPassword->value) {
            throw new PasswordAndRepeatPasswordDoNotMatchException();
        }
    }
}
