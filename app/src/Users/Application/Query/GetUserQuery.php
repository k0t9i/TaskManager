<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Domain\Bus\Query\QueryInterface;

final class GetUserQuery implements QueryInterface
{
    public function __construct(public readonly string $id)
    {
    }
}
