<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\Collection;

use App\ProjectRelationships\Domain\ValueObject\TaskLink;
use App\Shared\Domain\Collection\Collection;

final class TaskLinkCollection extends Collection
{
    protected function getType(): string
    {
        return TaskLink::class;
    }
}
