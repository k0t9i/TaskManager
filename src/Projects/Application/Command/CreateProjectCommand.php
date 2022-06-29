<?php
declare(strict_types=1);

namespace App\Projects\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

final class CreateProjectCommand implements CommandInterface
{
    public function __construct(
        public string $name,
        public string $description,
        public string $finishDate,
        public string $ownerId
    ) {
    }
}