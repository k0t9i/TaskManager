<?php
declare(strict_types=1);

namespace App\Requests\Domain\Collection;

use App\Requests\Domain\Entity\SameProjectRequest;
use App\Shared\Domain\Collection\Collection;

final class SameProjectRequestCollection extends Collection
{
    protected function getType(): string
    {
        return SameProjectRequest::class;
    }
}
