<?php

declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Service\PageableRepositoryInterface;
use App\Users\Domain\Entity\ProfileProjection;

/**
 * @method findAllByCriteria(Criteria $criteria): UserProjection[]
 */
interface UserQueryRepositoryInterface extends PageableRepositoryInterface
{
    public function findProfileByCriteria(Criteria $criteria): ?ProfileProjection;
}
