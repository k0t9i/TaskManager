<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Shared\Domain\ValueObject\DateTime;

abstract class DomainEvent
{
    public readonly string $occurredOn;

    public function __construct(
        public readonly string $aggregateId,
        string $occurredOn = null
    ) {
        $this->occurredOn = $occurredOn ?: (new DateTime())->getValue();
    }

    abstract public static function getEventName(): string;
    abstract public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static;
    abstract public function toPrimitives(): array;
}