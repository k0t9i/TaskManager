<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class LeaveProjectCommand implements CommandInterface
{
    public function __construct(
        public readonly string $membershipId,
        public readonly string $currentUserId
    ) {
    }
}