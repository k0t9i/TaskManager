<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\Entity\ProjectListProjection;
use App\Projects\Domain\Entity\ProjectProjection;
use App\Shared\Domain\Criteria\Criteria;

interface ProjectQueryRepositoryInterface
{
    /**
     * @return ProjectListProjection[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
    public function findByCriteria(Criteria $criteria): ?ProjectProjection;
}