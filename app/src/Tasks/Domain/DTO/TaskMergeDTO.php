<?php
declare(strict_types=1);

namespace App\Tasks\Domain\DTO;

use App\Tasks\Domain\Collection\TaskLinkCollection;

final class TaskMergeDTO
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $brief = null,
        public readonly ?string $description = null,
        public readonly ?string $startDate = null,
        public readonly ?string $finishDate = null,
        public readonly ?string $ownerId = null,
        public readonly ?int $status = null,
        public readonly ?TaskLinkCollection $links = null
    ) {
    }
}
