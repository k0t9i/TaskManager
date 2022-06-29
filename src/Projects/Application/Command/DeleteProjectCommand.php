<?php
declare(strict_types=1);

namespace App\Projects\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class DeleteProjectCommand implements CommandInterface
{
    public function __construct(
        public string $projectId,
        public string $currentUserId
    ) {
    }
}