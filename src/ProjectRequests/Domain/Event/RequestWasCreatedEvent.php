<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class RequestWasCreatedEvent extends DomainEvent
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly string $userId
    ) {
    }
}