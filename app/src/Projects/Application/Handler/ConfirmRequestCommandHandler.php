<?php

declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\ConfirmRequestCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\RequestId;
use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\Requests\ConfirmedRequestStatus;

final class ConfirmRequestCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(ConfirmRequestCommand $command): void
    {
        $requestId = new RequestId($command->id);
        $project = $this->repository->findByRequestId($requestId);
        if (null === $project) {
            throw new ProjectNotExistException();
        }

        $project->changeRequestStatus(
            $requestId,
            new ConfirmedRequestStatus(),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->repository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}
