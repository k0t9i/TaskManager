<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskWasDeletedEvent extends DomainEvent
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
