<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Projects\ProjectStatusWasChangedEvent;

final class ChangeStatusOnProjectStatusChangedSubscriber implements EventSubscriberInterface
{
    use RequestManagerSubscriberTrait;

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectStatusWasChangedEvent::class];
    }

    public function __invoke(ProjectStatusWasChangedEvent $event): void
    {
        $this->doInvoke($event->aggregateId, $event);
    }
}
