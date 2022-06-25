<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskWasDeletedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $taskId,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'projectTask.taskDeleted';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self(
            $aggregateId,
            $body['taskId'],
            $occurredOn
        );
    }

    public function toPrimitives(): array
    {
        return [
            'taskId' => $this->taskId,
        ];
    }
}
