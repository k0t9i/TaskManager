<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskLinkWasDeletedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $toTaskId,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'task.linkDeleted';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['toTaskId'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'toTaskId' => $this->toTaskId,
        ];
    }
}
