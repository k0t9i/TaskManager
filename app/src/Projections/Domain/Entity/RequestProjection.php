<?php

declare(strict_types=1);

namespace App\Projections\Domain\Entity;

use DateTime;

final class RequestProjection
{
    public function __construct(
        private string $id,
        private string $projectId,
        private int $status,
        private DateTime $changeDate,
        private string $userId,
        private string $userFirstname,
        private string $userLastname,
        private string $userEmail
    ) {
    }

    public function changeStatus(int $status, DateTime $changeDate): void
    {
        $this->status = $status;
        $this->changeDate = $changeDate;
    }

    public function changeUserProfile(string $firstname, string $lastname): void
    {
        $this->userFirstname = $firstname;
        $this->userLastname = $lastname;
    }
}
