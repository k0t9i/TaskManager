<?php
declare(strict_types=1);

namespace App\TaskManagers\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

class CloseTaskCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $currentUserId,
    ) {
    }
}