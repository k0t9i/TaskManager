<?php
declare(strict_types=1);

namespace App\Requests\Application\Subscriber;

use App\Requests\Application\Service\RequestManagerStateRecreator;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

trait RequestManagerSubscriberTrait
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly RequestManagerStateRecreator $stateRecreator,
        private readonly EventBusInterface $eventBus
    ) {
    }

    private function doInvoke(string $projectId, DomainEvent $event): void
    {
        $manager = $this->managerRepository->findByProjectId(new ProjectId($projectId));
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $newManager = $this->stateRecreator->fromEvent($manager, $event);

        $this->managerRepository->save($newManager);
        $this->eventBus->dispatch(...$newManager->releaseEvents());
    }
}
