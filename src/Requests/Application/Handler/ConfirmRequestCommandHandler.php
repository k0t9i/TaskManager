<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Command\ConfirmRequestCommand;
use App\Requests\Domain\Exception\RequestNotExistsException;
use App\Requests\Domain\Repository\RequestRepositoryInterface;
use App\Requests\Domain\ValueObject\ConfirmedRequestStatus;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

final class ConfirmRequestCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestRepositoryInterface $requestRepository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(ConfirmRequestCommand $command): void
    {
        $requestId = new RequestId($command->id);
        $request = $this->requestRepository->findById($requestId);
        if ($request === null) {
            throw new RequestNotExistsException();
        }

        $request->changeStatus(
            new ConfirmedRequestStatus(),
            new UserId($command->currentUserId)
        );

        $this->requestRepository->save($request);
        $this->eventBus->dispatch(...$request->releaseEvents());
    }
}