<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Requests\Application\Command\CreateRequestToProjectCommand;
use App\Requests\Domain\Collection\SameProjectRequestCollection;
use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\Entity\RequestProject;
use App\Requests\Domain\Entity\SameProjectRequest;
use App\Requests\Domain\Repository\RequestRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestProjectId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class CreateRequestToProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestRepositoryInterface $requestRepository,
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateRequestToProjectCommand $command): void
    {
        $project = $this->projectRepository->findById(new ProjectId($command->projectId));
        if ($project === null) {
            throw new ProjectNotExistException();
        }
        $user = $this->userRepository->findById(new UserId($command->userId));
        if ($user === null) {
            throw new UserNotExistException();
        }
        $requests = $this->requestRepository->findAllByProjectId($project->getId());
        $sameProjectRequests = new SameProjectRequestCollection();
        foreach ($requests as $request) {
            $sameProjectRequests->add(new SameProjectRequest(
                $request->getId(),
                $request->getUserId(),
                $request->getStatus()
            ));
        }

        $request = Request::create(
            new RequestId($this->uuidGenerator->generate()),
            $project->getId(),
            $user->getId(),
            new RequestProject(
                new RequestProjectId($this->uuidGenerator->generate()),
                $project->getStatus(),
                $project->getOwner()->userId,
                $project->getParticipants()->copyInnerCollection(),
                $sameProjectRequests
            )
        );

        $this->requestRepository->save($request);
        $this->eventBus->dispatch(...$request->releaseEvents());
    }
}