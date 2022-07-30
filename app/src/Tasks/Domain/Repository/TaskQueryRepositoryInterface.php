<?php

declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Service\PageableRepositoryInterface;
use App\Tasks\Domain\Entity\TaskProjection;

/**
 * @method findAllByCriteria(Criteria $criteria): TaskListProjection[]
 */
interface TaskQueryRepositoryInterface extends PageableRepositoryInterface
{
    public function findByCriteria(Criteria $criteria): ?TaskProjection;
}
