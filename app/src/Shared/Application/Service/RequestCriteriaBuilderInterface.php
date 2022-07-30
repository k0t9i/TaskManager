<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\RequestCriteriaDTO;

interface RequestCriteriaBuilderInterface
{
    public function build(array $request): RequestCriteriaDTO;
}
