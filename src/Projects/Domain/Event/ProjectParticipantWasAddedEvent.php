<?php
declare(strict_types=1);

namespace App\Projects\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectParticipantWasAddedEvent extends DomainEvent
{
    public function __construct(public readonly string $participantId)
    {
    }
}