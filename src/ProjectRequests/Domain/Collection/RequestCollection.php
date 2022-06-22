<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Collection;

use App\ProjectRequests\Domain\Entity\Request;
use App\Shared\Domain\Collection\Collection;

final class RequestCollection extends Collection
{
    protected function getType(): string
    {
        return Request::class;
    }
}
