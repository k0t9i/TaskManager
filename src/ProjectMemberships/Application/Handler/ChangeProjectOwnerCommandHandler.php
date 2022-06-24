<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Application\Handler;

use App\ProjectMemberships\Application\CQ\ChangeProjectOwnerCommand;
use App\ProjectMemberships\Domain\Repository\MembershipRepositoryInterface;
use App\ProjectMemberships\Domain\ValueObject\MembershipId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class ChangeProjectOwnerCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly MembershipRepositoryInterface $membershipRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(ChangeProjectOwnerCommand $command): void
    {
        $project = $this->membershipRepository->findById(new MembershipId($command->membershipId));
        $user = $this->userRepository->getById(new UserId($command->ownerId));

        $project->changeOwner(
            $user->getId(),
            new UserId($command->currentUserId)
        );

        $this->membershipRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}