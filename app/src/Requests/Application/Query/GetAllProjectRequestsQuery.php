<?php
declare(strict_types=1);

namespace App\Requests\Application\Query;

use App\Shared\Domain\Bus\Query\QueryInterface;

final class GetAllProjectRequestsQuery implements QueryInterface
{
    public function __construct(public readonly string $projectId)
    {
    }
}
