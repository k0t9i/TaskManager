<?php
declare(strict_types=1);

namespace App\Requests\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class RejectRequestCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $currentUserId
    ) {
    }
}