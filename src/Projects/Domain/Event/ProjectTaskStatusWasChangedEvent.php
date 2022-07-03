<?php
declare(strict_types=1);

namespace App\Projects\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectTaskStatusWasChangedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $projectTaskId,
        public readonly string $taskId,
        public readonly string $status,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'project.taskStatusChanged';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['projectTaskId'], $body['taskId'], $body['status'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'projectTaskId' => $this->projectTaskId,
            'taskId' => $this->taskId,
            'status' => $this->status,
        ];
    }
}
