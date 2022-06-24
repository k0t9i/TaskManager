<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\CQ\UpdateProjectInformationCommand;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;

final class UpdateProjectInformationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $repository,
        private readonly EventBusInterface $eventBus,
    ) {
    }

    public function __invoke(UpdateProjectInformationCommand $command): void
    {
        $project = $this->repository->getById(new ProjectId($command->projectId));

        $project->changeInformation(
            new ProjectName($command->name),
            new ProjectDescription($command->description),
            new DateTime($command->finishDate),
            new UserId($command->currentUserId)
        );

        $this->repository->update($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}