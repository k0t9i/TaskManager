<?php
declare(strict_types=1);

namespace App\Requests\Domain\Repository;

use App\Requests\Domain\Entity\RequestListProjection;
use App\Shared\Domain\Criteria\Criteria;

interface RequestQueryRepositoryInterface
{
    /**
     * @return RequestListProjection[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
}