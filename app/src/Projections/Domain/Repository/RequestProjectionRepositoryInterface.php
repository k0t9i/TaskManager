<?php
declare(strict_types=1);

namespace App\Projections\Domain\Repository;

use App\Projections\Domain\Entity\RequestProjection;

interface RequestProjectionRepositoryInterface
{
    /**
     * @param string $id
     * @return RequestProjection[]
     */
    public function findByUserId(string $id): array;
    public function findById(string $id): ?RequestProjection;
    public function save(RequestProjection $projection): void;
}