<?php
declare(strict_types=1);

namespace App\Requests\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class RequestWasCreatedEvent extends DomainEvent
{
    public function __construct(
        string $id,
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
        return new self($aggregateId, $body['requestId'], $body['userId'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'requestId' => $this->requestId,
            'userId' => $this->userId,
        ];
    }
}