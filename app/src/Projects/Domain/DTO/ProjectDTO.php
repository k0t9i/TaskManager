<?php
declare(strict_types=1);

namespace App\Projects\Domain\DTO;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Shared\Domain\Collection\UserIdCollection;

final class ProjectDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $finishDate,
        public readonly int $status,
        public readonly string $ownerId,
        public readonly string $ownerEmail,
        public readonly UserIdCollection $participantIds,
        public readonly ProjectTaskCollection $tasks
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['id'],
            $item['name'],
            $item['description'],
            $item['finish_date'],
            $item['status'],
            $item['owner_id'],
            $item['owner_email'],
            $item['participant_ids'],
            $item['tasks']
        );
    }
}
