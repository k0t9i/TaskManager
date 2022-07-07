<?php
declare(strict_types=1);

namespace App\Users\Domain\ValueObject;

use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserLastname;
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
        if ($this->repeatPassword === null) {
            return;
        }
        if ($this->password->value !== $this->repeatPassword->value) {
            throw new PasswordAndRepeatPasswordDoNotMatchException();
        }
    }
}
