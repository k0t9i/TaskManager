<?php
declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Users\Domain\Entity\ProfileProjection;
use App\Users\Domain\Entity\UserProjection;

interface UserQueryRepositoryInterface
{
    /**
     * @return UserProjection[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
    public function findByCriteria(Criteria $criteria): ?UserProjection;
    public function findProfileByCriteria(Criteria $criteria): ?ProfileProjection;
}