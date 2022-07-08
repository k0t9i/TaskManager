<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\TaskStatusWasChangedEvent;

final class ChangeTaskStatusOnTaskStatusChangedSubscriber implements EventSubscriberInterface
{
    use ProjectSubscriberTrait;

    public function subscribeTo(): array
    {
        return [TaskStatusWasChangedEvent::class];
    }

    public function __invoke(TaskStatusWasChangedEvent $event): void
    {
        $this->doInvoke($event->projectId, $event);
    }
}
