<?php
declare(strict_types=1);

namespace App\Projections\Domain\Entity;

final class UserProjection
{
    public function __construct(
        private string $id,
        private string $firstname,
        private string $lastname,
        private string $email,
        private string $userId,
        private ?string $ownerId = null,
        private ?string $projectId = null
    ) {
    }

    public function updateProfile(string $firstname, string $lastname): void
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }

    public function updateOwner(string $ownerId, self $other): void
    {
        if ($this->userId === $this->ownerId) {
            $this->userId = $ownerId;
            $this->ownerId = $ownerId;
            $this->firstname = $other->firstname;
            $this->lastname = $other->lastname;
            $this->email = $other->email;
        }
    }

    public function createCopyForProject(string $id, string $projectId): self
    {
        return new self(
            $id,
            $this->firstname,
            $this->lastname,
            $this->email,
            $this->userId,
            $this->ownerId,
            $projectId
        );
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }
}
