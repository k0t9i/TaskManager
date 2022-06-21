<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\ValueObject\ProjectRequestChangeDate;
use App\Projects\Domain\ValueObject\ProjectRequestId;
use App\Projects\Domain\ValueObject\ProjectRequestStatus;
use App\Projects\Domain\ValueObject\ProjectRequestUser;

final class ProjectRequest
{
    public function __construct(
        public readonly ProjectRequestId $id,
        public readonly Project $project,
        public readonly ProjectRequestUser $user,
        public readonly ProjectRequestStatus $status,
        public readonly ProjectRequestChangeDate $changeDate
    ) {
    }

    /**
     * @return ProjectRequestId
     */
    public function getId(): ProjectRequestId
    {
        return $this->id;
    }

    /**
     * @return Project
     */
    public function getProject(): Project
    {
        return $this->project;
    }

    /**
     * @return ProjectRequestUser
     */
    public function getUser(): ProjectRequestUser
    {
        return $this->user;
    }

    /**
     * @return ProjectRequestStatus
     */
    public function getStatus(): ProjectRequestStatus
    {
        return $this->status;
    }

    /**
     * @return ProjectRequestChangeDate
     */
    public function getChangeDate(): ProjectRequestChangeDate
    {
        return $this->changeDate;
    }

    /**
     * @param ProjectRequestStatus $status
     */
    private function setStatus(ProjectRequestStatus $status): void
    {
        $this->status = $status;
    }

    public function changeStatus(ProjectRequestStatus $status): void
    {
        $this->getStatus()->ensureCanBeChangedTo($status);
        $this->setStatus($status);
    }
}
