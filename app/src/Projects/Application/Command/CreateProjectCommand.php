<?php
declare(strict_types=1);

namespace App\Projects\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class CreateProjectCommand implements CommandInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $finishDate,
    ) {
    }

    public static function createFromRequest(array $item): self
    {
        return new self(
            $item['name'] ?? '',
            $item['description'] ?? '',
            $item['finish_date'] ?? ''
        );
    }
}