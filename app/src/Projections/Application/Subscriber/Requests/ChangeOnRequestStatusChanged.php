<?php

declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Requests;

use App\Projections\Domain\Repository\RequestProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Requests\RequestStatusWasChangedEvent;
use App\Shared\Domain\Exception\RequestNotExistsException;
use DateTime;
use Exception;

final class ChangeOnRequestStatusChanged implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestProjectionRepositoryInterface $requestRepository
    ) {
    }

    public function subscribeTo(): array
    {
        return [RequestStatusWasChangedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(RequestStatusWasChangedEvent $event): void
    {
        $projection = $this->requestRepository->findById($event->requestId);
        if (null === $projection) {
            throw new RequestNotExistsException($event->requestId);
        }

        $projection->changeStatus((int) $event->status, new DateTime($event->changeDate));

        $this->requestRepository->save($projection);
    }
}
