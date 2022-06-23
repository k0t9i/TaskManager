<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Shared\Domain\ValueObject\UserId;

class ProjectOwner
{
    public function __construct(public readonly UserId $userId)
    {
    }
}
