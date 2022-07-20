<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event\Tasks;

use App\Shared\Application\Bus\Event\DomainEvent;

final class TaskStatusWasChangedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $projectId,
        public readonly string $taskId,
        public readonly string $status,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'task.statusChanged';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['projectId'], $body['taskId'], $body['status'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'projectId' => $this->projectId,
            'taskId' => $this->taskId,
            'status' => $this->status,
        ];
    }
}
