<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectOwnerWasChangedEvent;

final class ChangeOwnerOnProjectOwnerChangedSubscriber implements EventSubscriberInterface
{
    use RequestManagerSubscriberTrait;

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectOwnerWasChangedEvent::class];
    }

    public function __invoke(ProjectOwnerWasChangedEvent $event): void
    {
        $this->doInvoke($event->aggregateId, $event);
    }
}
