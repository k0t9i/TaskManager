<?php

declare(strict_types=1);

namespace App\Tasks\Domain\Collection;

use App\Shared\Domain\Collection\Collection;
use App\Tasks\Domain\Entity\Task;

class TaskCollection extends Collection
{
    protected function getType(): string
    {
        return Task::class;
    }
}
