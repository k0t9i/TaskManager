<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\Repository;

use App\ProjectRelationships\Domain\Entity\Relationship;
use App\ProjectRelationships\Domain\ValueObject\RelationshipId;
use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;

interface RelationshipRepositoryInterface
{
    public function findById(RelationshipId $id): ?Relationship;
    public function findByTaskId(RelationshipTaskId $taskId): ?Relationship;
    public function update(Relationship $relationship): void;
}
