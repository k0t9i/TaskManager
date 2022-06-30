<?php
declare(strict_types=1);

namespace App\Requests\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class RequestStatusWasChangedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $requestId,
        public readonly string $status,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'requestManager.requestStatusChanged';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $body['requestId'], $body['status'], $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [
            'requestId' => $this->requestId,
            'status' => $this->status,
        ];
    }
}