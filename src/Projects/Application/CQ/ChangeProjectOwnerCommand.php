<?php
declare(strict_types=1);

namespace App\Projects\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class ChangeProjectOwnerCommand implements CommandInterface
{
    public function __construct(
        public string $projectId,
        public string $ownerId,
        public string $currentUserId
    ) {
    }
}