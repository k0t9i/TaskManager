<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\ProjectParticipantWasAddedEvent;

final class ChangeTaskDatesOnTaskInformationChangedSubscriber implements EventSubscriberInterface
{
    use ProjectSubscriberTrait;

    public function subscribeTo(): array
    {
        return [ProjectParticipantWasAddedEvent::class];
    }

    public function __invoke(ProjectParticipantWasAddedEvent $event): void
    {
        $this->doInvoke($event->projectId, $event);
    }
}
