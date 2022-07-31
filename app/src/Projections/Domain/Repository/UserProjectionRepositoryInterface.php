<?php

declare(strict_types=1);

namespace App\Projections\Domain\Repository;

use App\Projections\Domain\Entity\UserProjection;

interface UserProjectionRepositoryInterface
{
    /**
     * @return UserProjection[]
     */
    public function findAllByUserId(string $id): array;

    /**
     * @return UserProjection[]
     */
    public function findAllByProjectId(string $id): array;

    public function findByUserId(string $id): ?UserProjection;

    public function findByUserIdAndProjectId(string $id, string $projectId): ?UserProjection;

    public function save(UserProjection $projection): void;

    public function delete(UserProjection $projection): void;
}
