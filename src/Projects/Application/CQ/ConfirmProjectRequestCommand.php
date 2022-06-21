<?php
declare(strict_types=1);

namespace App\Projects\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class ConfirmProjectRequestCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $projectId,
        public string $currentUserId
    ) {
    }
}