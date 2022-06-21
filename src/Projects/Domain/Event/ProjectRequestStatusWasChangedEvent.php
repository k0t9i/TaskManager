<?php
declare(strict_types=1);

namespace App\Projects\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectRequestStatusWasChangedEvent extends DomainEvent
{
    public function __construct(
        public string $id,
        public readonly int $status
    ) {
    }
}