<?php
declare(strict_types=1);

namespace App\Projects\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class ProjectWasDeletedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'project.deleted';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new self($aggregateId, $occurredOn);
    }

    public function toPrimitives(): array
    {
        return [];
    }
}