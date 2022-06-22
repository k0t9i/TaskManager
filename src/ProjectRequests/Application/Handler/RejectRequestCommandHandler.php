<?php
declare(strict_types=1);

namespace App\ProjectRequests\Application\Handler;

use App\ProjectRequests\Application\CQ\RejectRequestCommand;
use App\ProjectRequests\Domain\Repository\RequestRepositoryInterface;
use App\ProjectRequests\Domain\ValueObject\RejectedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Users\Domain\ValueObject\UserId;

final class RejectRequestCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestRepositoryInterface $requestRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(RejectRequestCommand $command): void
    {
        $request = $this->requestRepository->getById(new RequestId($command->id));

        $request->changeStatus(
            new RejectedRequestStatus(),
            new UserId($command->currentUserId)
        );

        $this->requestRepository->update($request);
        $this->eventBus->dispatch(...$request->releaseEvents());
    }
}