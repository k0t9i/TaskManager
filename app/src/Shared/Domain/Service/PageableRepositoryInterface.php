<?php
declare(strict_types=1);

namespace App\Shared\Domain\Service;

use App\Shared\Domain\Criteria\Criteria;

interface PageableRepositoryInterface
{
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
}
