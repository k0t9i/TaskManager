<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\Entity\ProjectProjection;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Service\PageableRepositoryInterface;

/**
 * @method findAllByCriteria(Criteria $criteria): ProjectListProjection[]
 */
interface ProjectQueryRepositoryInterface extends PageableRepositoryInterface
{
    public function findByCriteria(Criteria $criteria): ?ProjectProjection;
}