<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Requests;

use App\Projections\Domain\Entity\RequestProjection;
use App\Projections\Domain\Repository\RequestProjectionRepositoryInterface;
use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\DomainEvent;
use App\Shared\Domain\Event\Requests\RequestWasCreatedEvent;
use App\Shared\Domain\Exception\UserNotExistException;
use DateTime;
use Exception;

final class CreateOnRequestCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestProjectionRepositoryInterface $requestRepository,
        private readonly UserProjectionRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [RequestWasCreatedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(RequestWasCreatedEvent $event): void
    {
        $user = $this->userRepository->findByUserId($event->userId);
        if ($user === null) {
            throw new UserNotExistException($event->userId);
        }

        $projection = new RequestProjection(
            $event->requestId,
            $event->projectId,
            (int) $event->status,
            new DateTime($event->changeDate),
            $event->userId,
            $user->getFirstname(),
            $user->getLastname(),
            $user->getEmail()
        );

        $this->requestRepository->save($projection);
    }
}
