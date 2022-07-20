<?php
declare(strict_types=1);

namespace App\Tasks\Application\Subscriber;

use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Tasks\Application\Service\TaskManagerStateRecreator;
use App\Tasks\Domain\Exception\TaskManagerNotExistException;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;

trait TaskManagerSubscriberTrait
{
    public function __construct(
        private readonly TaskManagerRepositoryInterface $managerRepository,
        private readonly TaskManagerStateRecreator $stateRecreator,
        private readonly EventBusInterface $eventBus
    ) {
    }

    private function doInvoke(string $projectId, DomainEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($projectId));
        if ($manager === null) {
            throw new TaskManagerNotExistException();
        }

        $newManager = $this->stateRecreator->fromEvent($manager, $event);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
