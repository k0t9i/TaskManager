<?php

declare(strict_types=1);

namespace App\Projections\Domain\Repository;

use App\Projections\Domain\Entity\TaskLinkProjection;

interface TaskLinkProjectionRepositoryInterface
{
    public function findById(string $id, string $toId): ?TaskLinkProjection;

    public function save(TaskLinkProjection $projection): void;

    public function delete(TaskLinkProjection $projection): void;
}
