<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Users\Domain\ValueObject\UserEmail;
use App\Users\Domain\ValueObject\UserFirstname;
use App\Users\Domain\ValueObject\UserId;
use App\Users\Domain\ValueObject\UserLastname;

final class ProjectRequestUser
{
    public function __construct(
        public readonly UserId $userId,
        public readonly UserFirstname $firstname,
        public readonly UserLastname $lastname,
        public readonly UserEmail $email
    ) {
    }
}
