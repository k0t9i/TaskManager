<?php
declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\CreateProjectCommand;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectInformation;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

final class CreateProjectCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticatorService,
    ) {
    }

    public function __invoke(CreateProjectCommand $command): void
    {

        $project = Project::create(
            new ProjectId($this->uuidGenerator->generate()),
            new ProjectInformation(
                new ProjectName($command->name),
                new ProjectDescription($command->description),
                new DateTime($command->finishDate)
            ),
            new Owner(
                $this->authenticatorService->getAuthUser()->getId()
            ),
        );

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}