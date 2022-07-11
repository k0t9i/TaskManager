<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use App\Shared\Domain\ValueObject\Users\UserId;

class UserIdCollection extends Collection
{
    protected function getType(): string
    {
        return UserId::class;
    }
}
