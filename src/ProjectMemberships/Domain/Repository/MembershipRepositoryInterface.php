<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Domain\Repository;

use App\ProjectMemberships\Domain\Entity\Membership;
use App\ProjectMemberships\Domain\ValueObject\MembershipId;

interface MembershipRepositoryInterface
{
    public function findById(MembershipId $id): Membership;
    public function update(Membership $membership): void;
}
