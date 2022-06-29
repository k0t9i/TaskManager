<?php
declare(strict_types=1);

namespace App\TaskManagers\Application\Command;

use App\Shared\Domain\Bus\Command\CommandInterface;

class UpdateTaskInformationCommand implements CommandInterface
{
    public function __construct(
        public string $id,
        public string $name,
        public string $brief,
        public string $description,
        public string $startDate,
        public string $finishDate,
        public string $currentUserId,
    ) {
    }
}