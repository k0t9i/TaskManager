<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Application\Bus\Query\QueryInterface;
use App\Shared\Application\DTO\RequestCriteriaDTO;

final class GetProjectTasksQuery implements QueryInterface
{
    public function __construct(
        public readonly string $projectId,
        public readonly RequestCriteriaDTO $criteria
    ) {
    }
}
