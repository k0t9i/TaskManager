<?php
declare(strict_types=1);

namespace App\Projects\Application\Command;

use App\Shared\Application\Bus\Command\CommandInterface;

final class UpdateProjectInformationCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?string $finishDate
    ) {
    }

    public static function createFromRequest(array $item): self
    {
        return new self(
            $item['id'],
            $item['name'] ?? null,
            $item['description'] ?? null,
            $item['finish_date'] ?? null,
        );
    }
}