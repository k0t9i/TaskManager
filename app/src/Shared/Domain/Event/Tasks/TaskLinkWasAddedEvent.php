<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event\Tasks;

use App\Shared\Application\Bus\Event\DomainEvent;

final class TaskLinkWasAddedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $fromTaskId,
        public readonly string $toTaskId,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'task.linkAdded';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['fromTaskId'], $body['toTaskId'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'fromTaskId' => $this->fromTaskId,
            'toTaskId' => $this->toTaskId,
        ];
    }
}
