<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectTaskStartDateWasChangedEvent;

final class ChangeTaskStartDateOnProjectTaskStartDateChangedSubscriber implements EventSubscriberInterface
{
    use TaskManagerSubscriberTrait;

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectTaskStartDateWasChangedEvent::class];
    }

    public function __invoke(ProjectTaskStartDateWasChangedEvent $event): void
    {
        $this->doInvoke($event->aggregateId, $event);
    }
}
