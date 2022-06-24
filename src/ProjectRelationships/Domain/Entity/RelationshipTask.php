<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\Entity;

use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\UserId;

final class RelationshipTask implements Hashable
{
    public function __construct(
        private RelationshipTaskId $id,
        private UserId $ownerId
    ) {
    }

    public function getId(): RelationshipTaskId
    {
        return $this->id;
    }

    public function getOwnerId(): UserId
    {
        return $this->ownerId;
    }

    public function getHash(): string
    {
        return $this->getId()->getHash();
    }
}
