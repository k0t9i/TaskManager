<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskStartDateWasChangedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $taskId,
        public readonly string $startDate,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'project.taskStartDateChanged';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['taskId'], $body['startDate'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'taskId' => $this->taskId,
            'startDate' => $this->startDate,
        ];
    }
}
