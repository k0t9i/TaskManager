<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Tasks\Domain\Entity\TaskListProjection;
use App\Tasks\Domain\Entity\TaskProjection;

interface TaskQueryRepositoryInterface
{
    /**
     * @return TaskListProjection[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
    public function findByCriteria(Criteria $criteria): ?TaskProjection;
}