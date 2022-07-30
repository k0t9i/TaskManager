<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Domain\Criteria\Criteria;

interface CriteriaFieldValidatorInterface
{
    public function validate(Criteria $criteria, string $class): void;
}
