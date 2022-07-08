<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectTaskFinishDateWasChangedEvent;

final class ChangeTaskFinishDateOnProjectTaskFinishDateChangedSubscriber implements EventSubscriberInterface
{
    use TaskManagerSubscriberTrait;

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectTaskFinishDateWasChangedEvent::class];
    }

    public function __invoke(ProjectTaskFinishDateWasChangedEvent $event): void
    {
        $this->doInvoke($event->aggregateId, $event);
    }
}
