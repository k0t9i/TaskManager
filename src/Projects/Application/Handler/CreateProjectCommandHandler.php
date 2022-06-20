<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\CQ\CreateProjectCommand;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectFinishDate;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectOwner;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserId;

final class CreateProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(CreateProjectCommand $command): void
    {
        $user = $this->userRepository->getById(new UserId($command->ownerId));

        $project = Project::create(
            new ProjectId($this->uuidGenerator->generate()),
            new ProjectName($command->name),
            new ProjectDescription($command->description),
            new ProjectFinishDate($command->finishDate),
            new ProjectOwner($user->getId())
        );

        $this->projectRepository->create($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}