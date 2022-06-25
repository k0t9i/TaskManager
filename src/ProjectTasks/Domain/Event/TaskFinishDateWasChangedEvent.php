<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskFinishDateWasChangedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $taskId,
        public readonly string $finishDate,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'project.taskFinishDateChanged';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['taskId'], $body['finishDate'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'taskId' => $this->taskId,
            'finishDate' => $this->finishDate,
        ];
    }
}
