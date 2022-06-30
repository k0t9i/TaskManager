<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Collection;

use App\Shared\Domain\Collection\Collection;
use App\Tasks\Domain\ValueObject\TaskLink;

final class TaskLinkCollection extends Collection
{
    protected function getType(): string
    {
        return TaskLink::class;
    }
}
