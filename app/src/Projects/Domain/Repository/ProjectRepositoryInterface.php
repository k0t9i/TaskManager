<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\Entity\Project;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

interface ProjectRepositoryInterface
{
    public function findById(ProjectId $id): ?Project;
    public function save(Project $project): void;
}