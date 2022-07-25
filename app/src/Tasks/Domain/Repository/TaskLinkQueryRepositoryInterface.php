<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Service\PageableRepositoryInterface;

/**
 * @method findAllByCriteria(Criteria $criteria): TaskLinkListProjection[]
 */
interface TaskLinkQueryRepositoryInterface extends PageableRepositoryInterface
{
}