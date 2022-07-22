<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Projects\Application\Service\ProjectEventHandler;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;

final class EventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectEventHandler $eventHandler
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
