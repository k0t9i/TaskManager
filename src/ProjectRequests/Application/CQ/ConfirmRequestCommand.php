<?php
declare(strict_types=1);

namespace App\ProjectRequests\Application\CQ;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class ConfirmRequestCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $currentUserId
    ) {
    }
}