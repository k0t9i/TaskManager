<?php
declare(strict_types=1);

namespace App\Tasks\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class AddLinkCommand implements CommandInterface
{
    public function __construct(
        public readonly string $fromTaskId,
        public readonly string $toTaskId,
        public readonly string $currentUserId,
    ) {
    }
}
