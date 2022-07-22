<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Requests\Application\Service\RequestManagerEventHandler;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;

final class EventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestManagerEventHandler $eventHandler
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
