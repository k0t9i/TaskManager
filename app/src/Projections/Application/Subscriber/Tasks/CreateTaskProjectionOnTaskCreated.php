<?php
declare(strict_types=1);

namespace App\Projections\Application\Subscriber\Tasks;

use App\Projections\Domain\Entity\TaskProjection;
use App\Projections\Domain\Repository\TaskProjectionRepositoryInterface;
use App\Shared\Application\Bus\Event\DomainEvent;
use App\Shared\Application\Bus\Event\EventSubscriberInterface;
use App\Shared\Domain\Event\Tasks\TaskWasCreatedEvent;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\Users\UserId;
use DateTime;
use Exception;

final class CreateTaskProjectionOnTaskCreated implements EventSubscriberInterface
{
    public function __construct(
        private readonly TaskProjectionRepositoryInterface $projectionRepository,
        private readonly SharedUserRepositoryInterface $userRepository
    ) {
    }

    /**
     * @return DomainEvent[]
     */
    public function subscribeTo(): array
    {
        return [TaskWasCreatedEvent::class];
    }

    /**
     * @throws Exception
     */
    public function __invoke(TaskWasCreatedEvent $event): void
    {
        $user = $this->userRepository->findById(new UserId($event->ownerId));
        if ($user === null) {
            throw new UserNotExistException($event->ownerId);
        }

        $projection = new TaskProjection(
            $event->taskId,
            $event->projectId,
            $event->name,
            $event->brief,
            $event->description,
            new DateTime($event->startDate),
            new DateTime($event->finishDate),
            (int) $event->status,
            $event->ownerId,
            $user->getFirstname()->value,
            $user->getLastname()->value,
            $user->getEmail()->value
        );

        $this->projectionRepository->save($projection);
    }
}
