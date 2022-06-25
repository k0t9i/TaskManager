<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\ValueObject;

use App\Shared\Domain\ValueObject\DateTime;

final class TaskInformation
{
    public function __construct(
        public readonly TaskName $name,
        public readonly TaskBrief $brief,
        public readonly TaskDescription $description,
        public readonly DateTime $startDate,
        public readonly DateTime $finishDate,
    ) {
    }
}
