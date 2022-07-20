<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Projects\Domain\DTO\ProjectResponseDTO;
use App\Shared\Domain\Criteria\Criteria;

interface ProjectQueryRepositoryInterface
{
    /**
     * @return ProjectListResponseDTO[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
    public function findByCriteria(Criteria $criteria): ?ProjectResponseDTO;
}