<?php
declare(strict_types=1);

namespace App\Tasks\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

class CloseTaskCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $currentUserId,
    ) {
    }
}