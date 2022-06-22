<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Collection;

use App\Shared\Domain\Collection\Collection;
use App\Users\Domain\ValueObject\UserId;

//TODO move to shared
class UserIdCollection extends Collection
{
    protected function getType(): string
    {
        return UserId::class;
    }
}
