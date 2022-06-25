<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

class TaskInformationWasChangedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $taskId,
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'projectTask.taskInformationChanged';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self(
            $aggregateId,
            $body['taskId'],
            $body['name'],
            $body['brief'],
            $body['description'],
            $body['startDate'],
            $body['finishDate'],
            $occurredOn
        );
    }

    public function toPrimitives(): array
    {
        return [
            'taskId' => $this->taskId,
            'name' => $this->name,
            'brief' => $this->brief,
            'description' => $this->description,
            'startDate' => $this->startDate,
            'finishDate' => $this->finishDate,
        ];
    }
}
