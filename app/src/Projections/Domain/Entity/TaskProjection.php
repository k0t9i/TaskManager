<?php

declare(strict_types=1);

namespace App\Projections\Domain\Entity;

use DateTime;

final class TaskProjection
{
    public function __construct(
        private string $id,
        private string $projectId,
        private string $name,
        private string $brief,
        private string $description,
        private DateTime $startDate,
        private DateTime $finishDate,
        private int $status,
        private string $ownerId,
        private string $ownerFirstname,
        private string $ownerLastname,
        private string $ownerEmail,
        private int $linksCount = 0
    ) {
    }

    public function updateInformation(
        string $name,
        string $brief,
        string $description,
        DateTime $startDate,
        DateTime $finishDate
    ): void {
        $this->name = $name;
        $this->brief = $brief;
        $this->description = $description;
        $this->startDate = $startDate;
        $this->finishDate = $finishDate;
    }

    public function changeStatus(int $status): void
    {
        $this->status = $status;
    }

    public function changeOwnerProfile(
        string $ownerFirstname,
        string $ownerLastname
    ): void {
        $this->ownerFirstname = $ownerFirstname;
        $this->ownerLastname = $ownerLastname;
    }

    public function incrementLinksCount()
    {
        ++$this->linksCount;
    }

    public function decrementLinksCount()
    {
        --$this->linksCount;
    }
}
