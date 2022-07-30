<?php

declare(strict_types=1);

namespace App\Projects\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class RejectRequestCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id
    ) {
    }
}
