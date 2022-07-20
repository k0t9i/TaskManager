<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectParticipantWasRemovedEvent;

final class RemoveParticipantOnProjectParticipantRemovedSubscriber implements EventSubscriberInterface
{
    use TaskManagerSubscriberTrait;

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [ProjectParticipantWasRemovedEvent::class];
    }

    public function __invoke(ProjectParticipantWasRemovedEvent $event): void
    {
        $this->doInvoke($event->aggregateId, $event);
    }
}
