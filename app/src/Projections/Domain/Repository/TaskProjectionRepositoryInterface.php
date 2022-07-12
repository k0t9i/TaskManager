<?php
declare(strict_types=1);

namespace App\Projections\Domain\Repository;

use App\Projections\Domain\Entity\TaskProjection;

interface TaskProjectionRepositoryInterface
{
    /**
     * @param string $id
     * @return TaskProjection[]
     */
    public function findAllByOwnerId(string $id): array;
    public function findById(string $id): ?TaskProjection;
    public function save(TaskProjection $projection): void;
}