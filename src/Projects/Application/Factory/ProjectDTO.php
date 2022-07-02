<?php
declare(strict_types=1);

namespace App\Projects\Application\Factory;

final class ProjectDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $finishDate,
        public readonly int $status,
        public readonly string $ownerId,
        public readonly array $participantIds = [],
        public readonly array $tasks = []
    ) {
    }
}
