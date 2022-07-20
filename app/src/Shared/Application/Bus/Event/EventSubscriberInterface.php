<?php
declare(strict_types=1);

namespace App\Shared\Application\Bus\Event;

interface EventSubscriberInterface
{
    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array;
}
