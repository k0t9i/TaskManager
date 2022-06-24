<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Application\Handler;

use App\ProjectMemberships\Application\CQ\LeaveProjectCommand;
use App\ProjectMemberships\Domain\Repository\MembershipRepositoryInterface;
use App\ProjectMemberships\Domain\ValueObject\MembershipId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

final class LeaveProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly MembershipRepositoryInterface $membershipRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(LeaveProjectCommand $command): void
    {
        $membership = $this->membershipRepository->findById(new MembershipId($command->membershipId));

        $currentUserId = new UserId($command->currentUserId);
        $membership->removeParticipant(
            $currentUserId,
            $currentUserId
        );

        $this->membershipRepository->update($membership);
        $this->eventBus->dispatch(...$membership->releaseEvents());
    }
}