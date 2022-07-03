<?php
declare(strict_types=1);

namespace App\Tasks\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

class CreateTaskCommand implements CommandInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $brief,
        public readonly string $description,
        public readonly string $startDate,
        public readonly string $finishDate,
        public readonly string $ownerId,
        public readonly string $projectId
    ) {
    }
}