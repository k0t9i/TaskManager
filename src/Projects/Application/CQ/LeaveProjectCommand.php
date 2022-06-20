<?php
declare(strict_types=1);

namespace App\Projects\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class LeaveProjectCommand implements CommandInterface
{
    public function __construct(
        public readonly string $projectId,
        public readonly string $currentUserId
    ) {
    }
}