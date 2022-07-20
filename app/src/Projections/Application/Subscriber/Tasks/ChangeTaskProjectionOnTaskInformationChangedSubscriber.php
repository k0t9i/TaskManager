<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Tasks;

use App\Projections\Domain\Repository\TaskProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Tasks\TaskInformationWasChangedEvent;
use App\Shared\Domain\Exception\TaskNotExistException;
use DateTime;
use Exception;

final class ChangeTaskProjectionOnTaskInformationChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [TaskInformationWasChangedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(TaskInformationWasChangedEvent $event): void
    {
        $projection = $this->projectionRepository->findById($event->taskId);
        if ($projection === null) {
            throw new TaskNotExistException($event->taskId);
        }

        $projection->updateInformation(
            $event->name,
            $event->brief,
            $event->description,
            new DateTime($event->startDate),
            new DateTime($event->finishDate)
        );
        $this->projectionRepository->save($projection);
    }
}
