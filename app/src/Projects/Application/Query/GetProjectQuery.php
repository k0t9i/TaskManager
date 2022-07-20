<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Shared\Application\Bus\Query\QueryInterface;

final class GetProjectQuery implements QueryInterface
{
    public function __construct(public readonly string $id)
    {
    }
}
