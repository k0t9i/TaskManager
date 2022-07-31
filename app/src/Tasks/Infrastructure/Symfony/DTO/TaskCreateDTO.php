<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Symfony\DTO;

use App\Tasks\Application\Command\CreateTaskCommand;

final class TaskCreateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
    ) {
    }

    public function createCommand(string $id, string $projectId, ?string $ownerId = null): CreateTaskCommand
    {
        return new CreateTaskCommand(
            $id,
            $this->name,
            $this->brief,
            $this->description,
            $this->startDate,
            $this->finishDate,
            $projectId,
            $ownerId
        );
    }
}
