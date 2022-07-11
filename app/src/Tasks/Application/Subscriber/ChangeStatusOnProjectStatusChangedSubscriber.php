<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectStatusWasChangedEvent;

final class ChangeStatusOnProjectStatusChangedSubscriber implements EventSubscriberInterface
{
    use TaskManagerSubscriberTrait;

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
