<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\TaskStatus;
use App\Tasks\Domain\Collection\TaskLinkCollection;
use App\Tasks\Domain\Exception\TaskLinkAlreadyExistsException;
use App\Tasks\Domain\Exception\TaskLinkNotExistException;
use App\Tasks\Domain\Exception\TasksOfTaskLinkAreEqualException;
use App\Tasks\Domain\Exception\TaskStartDateGreaterThanFinishDateException;
use App\Tasks\Domain\ValueObject\TaskInformation;
use App\Tasks\Domain\ValueObject\TaskLink;
use App\Tasks\Domain\ValueObject\TaskOwner;

final class Task implements Hashable
{
    public function __construct(
        private TaskId             $id,
        private TaskInformation    $information,
        private TaskOwner          $owner,
        private TaskStatus         $status,
        private TaskLinkCollection $links
    ) {
        $this->ensureFinishDateGreaterThanStart();
    }

    public function changeInformation(TaskInformation $information): void
    {
        $this->status->ensureAllowsModification();
        $this->information = $information;
        $this->ensureFinishDateGreaterThanStart();
    }

    public function changeStatus(TaskStatus $status): void
    {
        $this->status->ensureCanBeChangedTo($status);
        $this->status = $status;
    }

    public function addLink(TaskId $taskId): void
    {
        $this->ensureIsDifferentTask($taskId);
        $taskLink = new TaskLink($taskId);
        $this->ensureLinkDoesNotExist($taskLink);
        $this->links = $this->links->add($taskLink);
    }

    public function deleteLink(TaskId $taskId): void
    {
        $this->ensureIsDifferentTask($taskId);
        $taskLink = new TaskLink($taskId);
        $this->ensureLinkExists($taskLink);
        $this->links = $this->links->remove($taskLink);
    }

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getInformation(): TaskInformation
    {
        return $this->information;
    }

    public function getOwner(): TaskOwner
    {
        return $this->owner;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function getLinks(): TaskLinkCollection
    {
        return $this->links;
    }

    public function getHash(): string
    {
        return $this->id->getHash();
    }

    /**
     * @param self $other
     * @return bool
     */
    public function isEqual(object $other): bool
    {
        if (get_class($this) !== get_class($other)) {
            return false;
        }
        //FIXME should I add links to equality check?
        return $this->id->isEqual($other->id) &&
            $this->information->isEqual($other->information) &&
            $this->status->isEqual($other->status) &&
            $this->owner->userId->isEqual($other->owner->userId) &&
            $this->owner->userEmail->isEqual($other->owner->userEmail);
    }

    private function ensureFinishDateGreaterThanStart()
    {
        if ($this->information->startDate->isGreaterThan($this->information->finishDate)) {
            throw new TaskStartDateGreaterThanFinishDateException(
                $this->information->startDate->getValue(),
                $this->information->finishDate->getValue()
            );
        }
    }

    private function ensureLinkDoesNotExist(TaskLink $link): void
    {
        if ($this->links->exists($link)) {
            throw new TaskLinkAlreadyExistsException($this->id->value, $link->toTaskId->value);
        }
    }

    private function ensureLinkExists(TaskLink $link): void
    {
        if (!$this->links->exists($link)) {
            throw new TaskLinkNotExistException($this->id->value, $link->toTaskId->value);
        }
    }

    private function ensureIsDifferentTask(TaskId $toTaskId)
    {
        if ($this->id->isEqual($toTaskId)) {
            throw new TasksOfTaskLinkAreEqualException($toTaskId->value);
        }
    }
}
