<?php
declare(strict_types=1);

namespace App\Projects\Domain\DTO;

final class ProjectListResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $finishDate,
        public readonly string $ownerId,
        public readonly string $ownerFirstname,
        public readonly string $ownerLastname,
        public readonly string $ownerEmail,
        public readonly int $status,
        public readonly int $tasksCount,
        public readonly int $participantsCount,
        public readonly int $pendingRequestsCount,
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['id'],
            $item['user_id'],
            $item['name'],
            $item['description'],
            $item['finish_date'],
            $item['owner_id'],
            $item['owner_firstname'],
            $item['owner_lastname'],
            $item['owner_email'],
            $item['status'],
            $item['tasks_count'],
            $item['participants_count'],
            $item['pending_requests_count']
        );
    }
}
