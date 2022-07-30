<?php

declare(strict_types=1);

namespace App\Projections\Domain\Entity;

use DateTime;

final class ProjectProjection
{
    public function __construct(
        private string $id,
        private string $userId,
        private string $name,
        private string $description,
        private DateTime $finishDate,
        private int $status,
        private string $ownerId,
        private string $ownerFirstname,
        private string $ownerLastname,
        private string $ownerEmail,
        private int $tasksCount = 0,
        private int $pendingRequestsCount = 0,
        private int $participantsCount = 0
    ) {
    }

    public function createCopyForUser(string $userId): self
    {
        return new self(
            $this->id,
            $userId,
            $this->name,
            $this->description,
            $this->finishDate,
            $this->status,
            $this->ownerId,
            $this->ownerFirstname,
            $this->ownerLastname,
            $this->ownerEmail,
            $this->tasksCount,
            $this->pendingRequestsCount,
            $this->participantsCount,
        );
    }

    public function updateInformation(string $name, string $description, DateTime $finishDate): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->finishDate = $finishDate;
    }

    public function changeStatus(int $status): void
    {
        $this->status = $status;
    }

    public function changeOwner(
        string $ownerId,
        string $ownerFirstname,
        string $ownerLastname,
        string $ownerEmail
    ): void {
        if ($this->ownerId === $this->userId) {
            $this->userId = $ownerId;
        }
        $this->ownerId = $ownerId;
        $this->ownerFirstname = $ownerFirstname;
        $this->ownerLastname = $ownerLastname;
        $this->ownerEmail = $ownerEmail;
    }

    public function changeOwnerProfile(
        string $ownerFirstname,
        string $ownerLastname
    ): void {
        $this->ownerFirstname = $ownerFirstname;
        $this->ownerLastname = $ownerLastname;
    }

    public function incrementTasksCount()
    {
        ++$this->tasksCount;
    }

    public function incrementParticipantsCount()
    {
        ++$this->participantsCount;
    }

    public function decrementParticipantsCount()
    {
        --$this->participantsCount;
    }

    public function incrementPendingRequestsCount()
    {
        ++$this->pendingRequestsCount;
    }

    public function decrementPendingRequestsCount()
    {
        --$this->pendingRequestsCount;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
