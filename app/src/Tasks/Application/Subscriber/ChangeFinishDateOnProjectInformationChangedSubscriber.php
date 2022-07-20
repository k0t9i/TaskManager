<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectInformationWasChangedEvent;

final class ChangeFinishDateOnProjectInformationChangedSubscriber implements EventSubscriberInterface
{
    use TaskManagerSubscriberTrait;

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectInformationWasChangedEvent::class];
    }

    public function __invoke(ProjectInformationWasChangedEvent $event): void
    {
        $this->doInvoke($event->aggregateId, $event);
    }
}
