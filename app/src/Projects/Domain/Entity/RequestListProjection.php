<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

final class RequestListProjection
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly int $status,
        public readonly string $changeDate,
        public readonly string $userId,
        public readonly string $userFirstname,
        public readonly string $userLastname,
        public readonly string $userEmail,
        public readonly string $projectOwnerId
    ) {
    }
}
