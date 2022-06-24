<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\ValueObject;

final class TaskInformation
{
    public function __construct(
        public readonly TaskName $name,
        public readonly TaskBrief $brief,
        public readonly TaskDescription $description,
        public readonly TaskStartDate $startDate,
        public readonly TaskFinishDate $finishDate,
    ) {
    }
}
