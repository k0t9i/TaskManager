<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Symfony\DTO;

use App\Projects\Application\Command\UpdateProjectInformationCommand;

final class ProjectUpdateDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?string $finishDate
    ) {
    }

    public function createCommand(string $id): UpdateProjectInformationCommand
    {
        return new UpdateProjectInformationCommand(
            $id,
            $this->name,
            $this->description,
            $this->finishDate,
        );
    }
}
