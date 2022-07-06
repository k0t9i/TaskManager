<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class TaskWasCreatedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $projectId,
        public readonly string $taskId,
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
        public readonly string $ownerId,
        public readonly string $status,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'task.created';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self(
            $aggregateId,
            $body['projectId'],
            $body['taskId'],
            $body['name'],
            $body['brief'],
            $body['description'],
            $body['startDate'],
            $body['finishDate'],
            $body['ownerId'],
            $body['status'],
            $occurredOn
        );
    }

    public function toPrimitives(): array
    {
        return [
            'projectId' => $this->projectId,
            'taskId' => $this->taskId,
            'name' => $this->name,
            'brief' => $this->brief,
            'description' => $this->description,
            'startDate' => $this->startDate,
            'finishDate' => $this->finishDate,
            'ownerId' => $this->ownerId,
            'status' => $this->status,
        ];
    }
}
