<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Symfony\DTO;

use App\Projects\Application\Command\CreateProjectCommand;

final class ProjectCreateDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $finishDate,
    ) {
    }

    public function createCommand(string $id): CreateProjectCommand
    {
        return new CreateProjectCommand(
            $id,
            $this->name,
            $this->description,
            $this->finishDate,
        );
    }
}
