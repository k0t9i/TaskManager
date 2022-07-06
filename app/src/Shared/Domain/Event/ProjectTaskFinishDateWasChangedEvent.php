<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectTaskFinishDateWasChangedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $projectTaskId,
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
        return new self($aggregateId, $body['projectTaskId'], $body['taskId'], $body['finishDate'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'projectTaskId' => $this->projectTaskId,
            'taskId' => $this->taskId,
            'finishDate' => $this->finishDate,
        ];
    }
}
