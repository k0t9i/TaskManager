<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectParticipantWasRemovedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $participantId,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'membership.participantRemoved';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['participantId'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'participantId' => $this->participantId
        ];
    }
}