<?php

declare(strict_types=1);

namespace App\Projects\Application\Handler;

use App\Projects\Application\Command\UpdateProjectInformationCommand;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectInformation;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Exception\ProjectNotExistException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

final class UpdateProjectInformationCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(UpdateProjectInformationCommand $command): void
    {
        /** @var Project $project */
        $project = $this->projectRepository->findById(new ProjectId($command->id));
        if (null === $project) {
            throw new ProjectNotExistException($command->id);
        }

        $prevInfo = $project->getInformation();
        $project->changeInformation(
            new ProjectInformation(
                new ProjectName($command->name ?? $prevInfo->name->value),
                new ProjectDescription($command->description ?? $prevInfo->description->value),
                new DateTime($command->finishDate ?? $prevInfo->finishDate->getValue())
            ),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->projectRepository->save($project);
        $this->eventBus->dispatch(...$project->releaseEvents());
    }
}
