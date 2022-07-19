<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\DTO\RequestCriteriaDTO;

interface RequestCriteriaBuilderInterface
{
    public function build(array $request): RequestCriteriaDTO;
}
