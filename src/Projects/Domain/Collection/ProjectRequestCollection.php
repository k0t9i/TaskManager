<?php
declare(strict_types=1);

namespace App\Projects\Domain\Collection;

use App\Projects\Domain\Entity\ProjectRequest;
use App\Shared\Domain\Collection\Collection;

final class ProjectRequestCollection extends Collection
{
    protected function getType(): string
    {
        return ProjectRequest::class;
    }
}
