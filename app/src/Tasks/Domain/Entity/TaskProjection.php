<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

final class TaskProjection
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $projectId,
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
        public readonly string $ownerId,
        public readonly string $ownerFirstname,
        public readonly string $ownerLastname,
        public readonly string $ownerEmail,
        public readonly int $status
    ) {
    }
}
