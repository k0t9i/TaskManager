<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Repository;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\ValueObject\ProjectId;

class ProjectRepository implements ProjectRepositoryInterface
{
    public function findById(ProjectId $id): ?Project
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function save(Project $project): void
    {
        // TODO: Implement save() method.
    }
}