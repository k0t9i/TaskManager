<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class RequestStatusWasChangedEvent extends DomainEvent
{
    public function __construct(
        public string $id,
        public readonly int $status
    ) {
    }
}