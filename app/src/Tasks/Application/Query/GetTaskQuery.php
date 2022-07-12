<?php
declare(strict_types=1);

namespace App\Tasks\Application\Query;

use App\Shared\Domain\Bus\Query\QueryInterface;

final class GetTaskQuery implements QueryInterface
{
    public function __construct(public readonly string $id)
    {
    }
}
