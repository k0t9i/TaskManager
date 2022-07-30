<?php

declare(strict_types=1);

namespace App\Projects\Domain\Collection;

use App\Projects\Domain\Entity\ProjectTask;
use App\Shared\Domain\Collection\Collection;

final class ProjectTaskCollection extends Collection
{
    protected function getType(): string
    {
        return ProjectTask::class;
    }
}
