<?php
declare(strict_types=1);

namespace App\Projections\Domain\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;

#[Entity]
final class ProjectProjection
{
    public function __construct(
        #[Id]
        #[Column(type: Types::STRING)]
        private string $id,
        #[Id]
        #[Column(type: Types::STRING)]
        private string $userId,
        #[Column(type: Types::STRING)]
        private string $name,
        #[Column(type: Types::STRING)]
        private string $finishDate,
        #[Column(type: Types::INTEGER)]
        private int $status,
        #[Column(type: Types::STRING)]
        private string $ownerId,
        #[Column(type: Types::STRING)]
        private string $ownerFirstname,
        #[Column(type: Types::STRING)]
        private string $ownerLastname,
        #[Column(type: Types::STRING)]
        private string $ownerEmail,
        #[Column(type: Types::INTEGER)]
        private int $tasksCount = 0,
        #[Column(type: Types::INTEGER)]
        private int $pendingRequestsCount = 0,
        #[Column(type: Types::INTEGER)]
        private int $participantsCount = 0
    ) {
    }

    public function createCopyForUser(string $userId): self
    {
        return new ProjectProjection(
            $this->id,
            $userId,
            $this->name,
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

    public function updateInformation(string $name, $finishDate): void
    {
        $this->name = $name;
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
        $this->tasksCount++;
    }

    public function incrementParticipantsCount()
    {
        $this->participantsCount++;
    }

    public function decrementParticipantsCount()
    {
        $this->participantsCount--;
    }

    public function incrementPendingRequestsCount()
    {
        $this->pendingRequestsCount++;
    }

    public function decrementPendingRequestsCount()
    {
        $this->pendingRequestsCount--;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }
}
