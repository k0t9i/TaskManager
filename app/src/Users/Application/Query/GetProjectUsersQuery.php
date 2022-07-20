<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Application\DTO\RequestCriteriaDTO;
use App\Shared\Domain\Bus\Query\QueryInterface;

final class GetProjectUsersQuery implements QueryInterface
{
    public function __construct(
        public readonly string $projectId,
        public readonly RequestCriteriaDTO $criteria
    ) {
    }
}
