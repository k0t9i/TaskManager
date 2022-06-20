<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Users\Domain\ValueObject\UserId;

final class ProjectParticipant
{
    public function __construct(
        public readonly UserId $userId
    ) {
    }
}
