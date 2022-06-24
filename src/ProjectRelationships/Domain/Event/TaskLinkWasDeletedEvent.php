<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskLinkWasDeletedEvent extends DomainEvent
{
    public function __construct(
        public readonly string $fromTaskId,
        public readonly string $toTaskId
    ) {
    }
}
