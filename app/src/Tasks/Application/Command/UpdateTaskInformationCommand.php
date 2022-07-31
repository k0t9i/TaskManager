<?php

declare(strict_types=1);

namespace App\Tasks\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class UpdateTaskInformationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name,
        public readonly ?string $brief,
        public readonly ?string $description,
        public readonly ?string $startDate,
        public readonly ?string $finishDate
    ) {
    }
}
