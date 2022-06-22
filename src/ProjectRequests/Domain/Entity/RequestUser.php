<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Entity;

use App\Users\Domain\ValueObject\UserEmail;
use App\Users\Domain\ValueObject\UserFirstname;
use App\Users\Domain\ValueObject\UserId;
use App\Users\Domain\ValueObject\UserLastname;

final class RequestUser
{
    public function __construct(
        public readonly UserId $userId,
        public readonly UserFirstname $firstname,
        public readonly UserLastname $lastname,
        public readonly UserEmail $email
    ) {
    }
}
