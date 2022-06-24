<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\Collection;

use App\ProjectRelationships\Domain\Entity\RelationshipTask;
use App\Shared\Domain\Collection\Collection;

final class RelationshipTaskCollection extends Collection
{
    protected function getType(): string
    {
        return RelationshipTask::class;
    }
}
