<?php
declare(strict_types=1);

namespace App\Requests\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Service\PageableRepositoryInterface;

/**
 * @method findAllByCriteria(Criteria $criteria): RequestListProjection[]
 */
interface RequestQueryRepositoryInterface extends PageableRepositoryInterface
{
}