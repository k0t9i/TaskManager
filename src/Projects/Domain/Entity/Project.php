<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\Event\ProjectInformationWasChangedEvent;
use App\Projects\Domain\Event\ProjectStatusWasChangedEvent;
use App\Projects\Domain\Event\ProjectWasCreatedEvent;
use App\Projects\Domain\Factory\ProjectStatusFactory;
use App\Projects\Domain\ValueObject\ActiveProjectStatus;
use App\Projects\Domain\ValueObject\ClosedProjectStatus;
use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Projects\Domain\ValueObject\ProjectId;
use App\Projects\Domain\ValueObject\ProjectName;
use App\Projects\Domain\ValueObject\ProjectOwner;
use App\Projects\Domain\ValueObject\ProjectStatus;
use App\ProjectTasks\Domain\Collection\TaskCollection;
use App\ProjectTasks\Domain\Entity\Task;
use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\UserId;

final class Project extends AggregateRoot
{
    /**
     * @var Task[]|TaskCollection
     */
    private TaskCollection $tasks;

    public function __construct(
        private ProjectId $id,
        private ProjectName $name,
        private ProjectDescription $description,
        private DateTime $finishDate,
        private ProjectStatus $status,
        private ProjectOwner $owner
    ) {
        $this->tasks = new TaskCollection();
    }

    /**
     * @return ProjectId
     */
    public function getId(): ProjectId
    {
        return $this->id;
    }

    /**
     * @return ProjectName
     */
    public function getName(): ProjectName
    {
        return $this->name;
    }

    /**
     * @return ProjectDescription
     */
    public function getDescription(): ProjectDescription
    {
        return $this->description;
    }

    public function getFinishDate(): DateTime
    {
        return $this->finishDate;
    }

    public function getOwner(): ProjectOwner
    {
        return $this->owner;
    }

    /**
     * @return Task[]
     */
    public function getTasks(): TaskCollection
    {
        return $this->tasks;
    }

    public function setTasks(TaskCollection $tasks): void
    {
        $this->tasks = $tasks;
    }

    public static function create(
        ProjectId $id,
        ProjectName $name,
        ProjectDescription $description,
        DateTime $finishDate,
        ProjectOwner $owner
    ): self {
        $status = new ActiveProjectStatus();
        $project = new self($id, $name, $description, $finishDate, $status, $owner);

        $project->registerEvent(new ProjectWasCreatedEvent(
            $id->value,
            $name->value,
            $description->value,
            $finishDate->getValue(),
            ProjectStatusFactory::scalarFromObject($status),
            $owner->userId->value
        ));

        return $project;
    }

    public function ensureIsOwner(UserId $userId): void
    {
        if (!$this->isOwner($userId)) {
            throw new UserIsNotOwnerException();
        }
    }

    public function isOwner(UserId $userId): bool
    {
        return $this->owner->userId->isEqual($userId);
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
     * @return ProjectStatus
     */
    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    /**
     * @param ProjectStatus $status
     */
    public function changeStatus(ProjectStatus $status, UserId $currentUserId): void
    {
        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->ensureIsOwner($currentUserId);

        if ($status instanceof ClosedProjectStatus) {
            foreach ($this->tasks as $task) {
                $task->closeTaskIfProjectWasClosed($this);
            }
        }
        $this->status = $status;

        $this->registerEvent(new ProjectStatusWasChangedEvent(
            $this->getId()->value,
            ProjectStatusFactory::scalarFromObject($status)
        ));
    }
}