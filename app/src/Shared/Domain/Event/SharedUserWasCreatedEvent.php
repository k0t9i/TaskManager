<?php
declare(strict_types=1);

namespace App\Shared\Domain\Event;

use App\Shared\Domain\Bus\Event\DomainEvent;

final class SharedUserWasCreatedEvent extends DomainEvent
{
    public function __construct(
        string $id,
        public readonly string $email,
        public readonly string $firstname,
        public readonly string $lastname,
        string $occurredOn = null
    ) {
        parent::__construct($id, $occurredOn);
    }

    public static function getEventName(): string
    {
        return 'sharedUser.created';
    }

    public static function fromPrimitives(string $aggregateId, array $body, string $occurredOn): static
    {
        return new static(
            $aggregateId,
            $body['email'],
            $body['firstname'],
            $body['lastname'],
            $occurredOn
        );
    }

    public function toPrimitives(): array
    {
        return [
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
        ];
    }
}
