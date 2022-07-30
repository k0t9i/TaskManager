<?php
declare(strict_types=1);

namespace App\Projects\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class CreateProjectCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $finishDate,
    ) {
    }

    public static function createFromRequest(string $id, array $item): self
    {
        return new self(
            $id,
            $item['name'] ?? '',
            $item['description'] ?? '',
            $item['finish_date'] ?? ''
        );
    }
}