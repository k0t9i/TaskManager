<?php
declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\UserId;

final class TaskOwner
{
    public function __construct(
        public readonly UserId $userId,
        public readonly Email $userEmail,
    ) {
    }
}
