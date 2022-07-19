<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Shared\Application\DTO\RequestCriteriaDTO;
use App\Shared\Domain\Bus\Query\QueryInterface;

final class GetAllOwnProjectsQuery implements QueryInterface
{
    public function __construct(
        public readonly RequestCriteriaDTO $criteria
    ) {
    }
}
