<?php

declare(strict_types=1);

namespace App\Projects\Domain\Collection;

use App\Projects\Domain\Entity\Request;
use App\Shared\Domain\Collection\Collection;

final class RequestCollection extends Collection
{
    protected function getType(): string
    {
        return Request::class;
    }
}
