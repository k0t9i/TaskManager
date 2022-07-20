<?php
declare(strict_types=1);

namespace App\Shared\Application\Bus\Event;

use App\Shared\Domain\Event\DomainEvent;

interface EventSubscriberInterface
{
    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array;
}
