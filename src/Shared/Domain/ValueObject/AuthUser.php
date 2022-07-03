<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final class AuthUser
{
    public function __construct(
        public readonly UserId $userId
    ) {
    }
}
