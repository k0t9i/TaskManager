<?php
declare(strict_types=1);

namespace App\Projects\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectInformationWasChangedEvent extends DomainEvent
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $finishDate,
    ) {
    }
}