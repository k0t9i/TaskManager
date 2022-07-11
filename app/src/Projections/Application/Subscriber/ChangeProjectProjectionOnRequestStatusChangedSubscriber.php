<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber;

use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Requests\RequestStatusWasChangedEvent;
use App\Shared\Domain\ValueObject\Requests\RequestStatus;

final class ChangeProjectProjectionOnRequestStatusChangedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly ProjectProjectionRepositoryInterface $projectionRepository,
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [RequestStatusWasChangedEvent::class];
    }

    public function __invoke(RequestStatusWasChangedEvent $event): void
    {
        $status = RequestStatus::createFromScalar((int) $event->status);
        if (!$status->isPending()) {
            $projections = $this->projectionRepository->findAllById($event->projectId);

            foreach ($projections as $projection) {
                $projection->decrementPendingRequestsCount();
                $this->projectionRepository->save($projection);
            }
        }
    }
}