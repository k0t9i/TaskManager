<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectTaskStatusWasChangedEvent;

final class ChangeTaskStatusOnProjectTaskStatusChangedSubscriber implements EventSubscriberInterface
{
    use TaskManagerSubscriberTrait;

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectTaskStatusWasChangedEvent::class];
    }

    public function __invoke(ProjectTaskStatusWasChangedEvent $event): void
    {
        $this->doInvoke($event->aggregateId, $event);
    }
}
