<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Tasks\TaskWasCreatedEvent;

final class CreateTaskOnTaskCreatedSubscriber implements EventSubscriberInterface
{
    use ProjectSubscriberTrait;

    public function subscribeTo(): array
    {
        return [TaskWasCreatedEvent::class];
    }

    public function __invoke(TaskWasCreatedEvent $event): void
    {
        $this->doInvoke($event->projectId, $event);
    }
}
