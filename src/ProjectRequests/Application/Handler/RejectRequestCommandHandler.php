<?php
declare(strict_types=1);

namespace App\ProjectRequests\Application\Handler;

use App\ProjectRequests\Application\Command\RejectRequestCommand;
use App\ProjectRequests\Domain\Exception\ProjectRequestNotExistsException;
use App\ProjectRequests\Domain\Repository\ProjectRequestRepositoryInterface;
use App\ProjectRequests\Domain\ValueObject\RejectedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

final class RejectRequestCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRequestRepositoryInterface $projectRequestRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(RejectRequestCommand $command): void
    {
        $requestId = new RequestId($command->id);
        $project = $this->projectRequestRepository->findByRequestId($requestId);
        if ($project === null) {
            throw new ProjectRequestNotExistsException();
        }

        $project->changeRequestStatus(
            $requestId,
            new RejectedRequestStatus(),
            new UserId($command->currentUserId)
        );

        $this->projectRequestRepository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}