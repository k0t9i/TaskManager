<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Requests\RequestStatusWasChangedEvent;

final class AddParticipantOnRequestConfirmedSubscriber implements EventSubscriberInterface
{
    use ProjectSubscriberTrait;

    public function subscribeTo(): array
    {
        return [RequestStatusWasChangedEvent::class];
    }

    public function __invoke(RequestStatusWasChangedEvent $event): void
    {
        $this->doInvoke($event->projectId, $event);
    }
}
