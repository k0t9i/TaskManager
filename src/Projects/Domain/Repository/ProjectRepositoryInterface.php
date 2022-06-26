<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\ValueObject\ProjectId;

interface ProjectRepositoryInterface
{
    public function findById(ProjectId $id): ?Project;
    public function findByOwnerId(string $ownerId): ?Project;
    public function create(Project $project): void;
    public function update(Project $project): void;
    public function delete(Project $project): void;
}