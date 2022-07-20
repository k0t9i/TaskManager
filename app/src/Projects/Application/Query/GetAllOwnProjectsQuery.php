<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Shared\Application\Bus\Query\QueryInterface;
use App\Shared\Application\DTO\RequestCriteriaDTO;

final class GetAllOwnProjectsQuery implements QueryInterface
{
    public function __construct(
        public readonly RequestCriteriaDTO $criteria
    ) {
    }
}
