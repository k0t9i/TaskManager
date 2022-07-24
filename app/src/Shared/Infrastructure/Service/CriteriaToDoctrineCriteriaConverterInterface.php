<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Criteria\Criteria;
use Doctrine\Common\Collections\Criteria as DoctrineCriteria;

interface CriteriaToDoctrineCriteriaConverterInterface
{
    public function convert(Criteria $criteria): DoctrineCriteria;
}
