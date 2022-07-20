<?php
declare(strict_types=1);

namespace App\Projects\Application\Subscriber;

use App\Projects\Application\Service\ProjectStateRecreator;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

trait ProjectSubscriberTrait
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly ProjectStateRecreator $stateRecreator,
        private readonly EventBusInterface $eventBus
    ) {
    }

    private function doInvoke(string $projectId, DomainEvent $event): void
    {
        $project = $this->projectRepository->findById(new ProjectId($projectId));
        if ($project === null) {
            throw new ProjectNotExistException($projectId);
        }

        $project = $this->stateRecreator->fromEvent($project, $event);

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}
