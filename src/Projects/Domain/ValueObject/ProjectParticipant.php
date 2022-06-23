<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\UserId;

final class ProjectParticipant implements Hashable
{
    public function __construct(
        public readonly UserId $userId
    ) {
    }

    public function getHash(): string
    {
        return $this->userId->getHash();
    }
}
