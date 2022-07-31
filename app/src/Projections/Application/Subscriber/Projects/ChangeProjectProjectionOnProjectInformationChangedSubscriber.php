<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Projects;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Projects\ProjectInformationWasChangedEvent;
use DateTime;
use Exception;

final class ChangeProjectProjectionOnProjectInformationChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    public function subscribeTo(): array
    {
        return [ProjectInformationWasChangedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(ProjectInformationWasChangedEvent $event): void
    {
        $projections = $this->projectionRepository->findAllById($event->aggregateId);

        foreach ($projections as $projection) {
            $projection->updateInformation(
                $event->name,
                $event->description,
                new DateTime($event->finishDate)
            );
            $this->projectionRepository->save($projection);
        }
    }
}
