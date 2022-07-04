<?php
declare(strict_types=1);

namespace App\Projects\Application\Query;

use App\Shared\Domain\Bus\Query\QueryInterface;

final class GetAllOwnProjectsQuery implements QueryInterface
{
    public function __construct()
    {
    }
}
