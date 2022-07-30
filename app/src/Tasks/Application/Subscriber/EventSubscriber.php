<?php

declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Tasks\Application\Service\TaskManagerEventHandler;

final class EventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskManagerEventHandler $eventHandler
    ) {
    }

    public function subscribeTo(): array
    {
        return [];
    }

    public function __invoke(DomainEvent $event): void
    {
        $this->eventHandler->handle($event);
    }
}
