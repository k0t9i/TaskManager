<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event\Requests;

use App\Shared\Domain\Event\DomainEvent;

final class RequestWasCreatedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $projectId,
        public readonly string $requestId,
        public readonly string $userId,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'request.created';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self(
            $aggregateId,
            $body['projectId'],
            $body['requestId'],
            $body['userId'],
            $occurredOn
        );
    }

    public function toPrimitives(): array
    {
        return [
            'projectId' => $this->projectId,
            'requestId' => $this->requestId,
            'userId' => $this->userId,
        ];
    }
}