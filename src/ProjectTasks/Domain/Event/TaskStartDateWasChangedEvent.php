<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskStartDateWasChangedEvent extends DomainEvent
{
    public function __construct(
        public readonly string $id,
        public readonly string $startDate
    ) {
    }
}