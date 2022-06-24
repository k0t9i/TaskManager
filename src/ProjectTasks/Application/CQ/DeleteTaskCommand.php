<?php
declare(strict_types=1);

namespace App\ProjectTasks\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

class DeleteTaskCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $currentUserId,
    ) {
    }
}