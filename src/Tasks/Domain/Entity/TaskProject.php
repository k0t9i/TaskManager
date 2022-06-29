<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;
use App\Tasks\Domain\Exception\InsufficientPermissionsToChangeTaskException;
use App\Tasks\Domain\Exception\TaskFinishDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\Exception\TaskStartDateGreaterThanProjectFinishDateException;
use App\Tasks\Domain\ValueObject\TaskProjectId;

final class TaskProject
{
    //TODO change project status
    //TODO change project owner
    //TODO change project information
    //TODO change project task start date
    //TODO change project task finish date
    //TODO change project task status
    //TODO add project participant status
    //TODO remove project participant status
    public function __construct(
        private TaskProjectId    $id,
        private ProjectStatus    $status,
        private UserId           $ownerId,
        private DateTime         $finishDate,
        private UserIdCollection $participantIds
    ) {
    }

    public function getId(): TaskProjectId
    {
        return $this->id;
    }

    public function getStatus(): ProjectStatus
    {
        return $this->status;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getFinishDate(): DateTime
    {
        return $this->finishDate;
    }

    public function getParticipantIds(): UserIdCollection
    {
        return $this->participantIds;
    }

    public function ensureCanChangeTask(UserId $taskOwnerId, UserId $currentUserId): void
    {
        $this->status->ensureAllowsModification();
        if (!$this->isOwner($currentUserId) && $taskOwnerId->isEqual($currentUserId)) {
            throw new InsufficientPermissionsToChangeTaskException();
        }
    }

    public function ensureIsFinishDateGreaterThanTaskDates(DateTime $startDate, DateTime $finishDate): void
    {
        if ($startDate->isGreaterThan($this->finishDate)) {
            throw new TaskStartDateGreaterThanProjectFinishDateException();
        }
        if ($finishDate->isGreaterThan($this->finishDate)) {
            throw new TaskFinishDateGreaterThanProjectFinishDateException();
        }
    }

    public function isUserInProject(UserId $userId): bool
    {
        return $this->isOwner($userId) || $this->isParticipant($userId);
    }

    private function isOwner(UserId $userId): bool
    {
        return $this->ownerId->isEqual($userId);
    }

    private function isParticipant(UserId $userId): bool
    {
        return $this->participantIds->hashExists($userId->getHash());
    }
}
