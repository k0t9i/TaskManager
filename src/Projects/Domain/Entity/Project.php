<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\Event\ProjectInformationWasChangedEvent;
use App\Projects\Domain\Event\ProjectStatusWasChangedEvent;
use App\Projects\Domain\Event\ProjectWasCreatedEvent;
use App\Projects\Domain\ValueObject\ActiveProjectStatus;
use App\Projects\Domain\ValueObject\ClosedProjectStatus;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;

final class Project extends AggregateRoot
{

    public function __construct(
        private ProjectId $id,
        private ProjectName $name,
        private ProjectDescription $description,
        private DateTime $finishDate,
        private ProjectStatus $status,
        private UserId $ownerId,
        private ProjectTaskCollection $tasks
    ) {
    }

    public static function create(
        ProjectId $id,
        ProjectName $name,
        ProjectDescription $description,
        DateTime $finishDate,
        UserId $ownerId
    ): self {
        $status = new ActiveProjectStatus();
        $project = new self(
            $id,
            $name,
            $description,
            $finishDate,
            $status,
            $ownerId,
            new ProjectTaskCollection()
        );

        $project->registerEvent(new ProjectWasCreatedEvent(
            $id->value,
            $name->value,
            $description->value,
            $finishDate->getValue(),
            (string) $status->getScalar(),
            $ownerId->value
        ));

        return $project;
    }

    public function changeInformation(
        ProjectName $name,
        ProjectDescription $description,
        DateTime $finishDate,
        UserId $currentUserId
    ): void {
        $this->getStatus()->ensureAllowsModification();
        $this->ensureIsOwner($currentUserId);

        $this->name = $name;
        $this->description = $description;
        $this->finishDate = $finishDate;

        /** @var ProjectTask $task */
        foreach ($this->tasks as $task) {
            $task->limitDatesByProjectFinishDate($this);
        }

        $this->registerEvent(new ProjectInformationWasChangedEvent(
            $this->getId()->value,
            $this->name->value,
            $this->description->value,
            $this->finishDate->getValue()
        ));
    }

    /**
     * @param ProjectStatus $status
     */
    public function changeStatus(ProjectStatus $status, UserId $currentUserId): void
    {
        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->ensureIsOwner($currentUserId);

        if ($status instanceof ClosedProjectStatus) {
            /** @var ProjectTask $task */
            foreach ($this->tasks as $task) {
                $task->closeTaskIfProjectWasClosed($this);
            }
        }
        $this->status = $status;

        $this->registerEvent(new ProjectStatusWasChangedEvent(
            $this->getId()->value,
            (string) $status->getScalar()
        ));
    }

    public function getId(): ProjectId
    {
        return $this->id;
    }

    public function getName(): ProjectName
    {
        return $this->name;
    }

    public function getDescription(): ProjectDescription
    {
        return $this->description;
    }

    public function getFinishDate(): DateTime
    {
        return $this->finishDate;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    /**
     * @return ProjectTaskCollection|ProjectTask[]
     */
    public function getTasks(): ProjectTaskCollection
    {
        return $this->tasks;
    }

    public function ensureIsOwner(UserId $userId): void
    {
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->ownerId->isEqual($userId);
    }
}