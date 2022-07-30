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

    public static function createFromRequest(array $item): self
    {
        return new self(
            $item['id'],
            $item['name'] ?? null,
            $item['brief'] ?? null,
            $item['description'] ?? null,
            $item['start_date'] ?? null,
            $item['finish_date'] ?? null,
        );
    }
}
