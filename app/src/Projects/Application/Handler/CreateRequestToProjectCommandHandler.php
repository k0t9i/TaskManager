<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\CreateRequestToProjectCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\RequestId;
use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

final class CreateRequestToProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $repository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticatorService,
    ) {
    }

    public function __invoke(CreateRequestToProjectCommand $command): void
    {
        $project = $this->repository->findById(new ProjectId($command->projectId));
        if ($project === null) {
            throw new ProjectNotExistException($command->projectId);
        }

        $project->createRequest(
            new RequestId($this->uuidGenerator->generate()),
            $this->authenticatorService->getAuthUser()->getId()
        );

        $this->repository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}