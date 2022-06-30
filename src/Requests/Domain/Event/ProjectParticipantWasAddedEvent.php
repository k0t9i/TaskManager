<?php
declare(strict_types=1);

namespace App\Requests\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectParticipantWasAddedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $projectId,
        public readonly string $participantId,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'requestManager.participantAdded';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['projectId'], $body['participantId'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'projectId' => $this->projectId,
            'participantId' => $this->participantId,
        ];
    }
}