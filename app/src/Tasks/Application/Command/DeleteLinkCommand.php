<?php
declare(strict_types=1);

namespace App\Tasks\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class DeleteLinkCommand implements CommandInterface
{
    public function __construct(
        public readonly string $fromTaskId,
        public readonly string $toTaskId
    ) {
    }
}
