<?php
declare(strict_types=1);

namespace App\Projections\Domain\Repository;

use App\Projections\Domain\Entity\ProjectProjection;

interface ProjectProjectionRepositoryInterface
{
    /**
     * @param string $id
     * @return ProjectProjection[]
     */
    public function findAllById(string $id): array;
    /**
     * @param string $id
     * @return ProjectProjection[]
     */
    public function findAllByOwnerId(string $id): array;
    public function save(ProjectProjection $projection): void;
    public function delete(ProjectProjection $projection): void;
}