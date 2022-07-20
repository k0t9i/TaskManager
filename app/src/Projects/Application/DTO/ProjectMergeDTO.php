<?php
declare(strict_types=1);

namespace App\Projects\Application\DTO;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Shared\Domain\Collection\UserIdCollection;

final class ProjectMergeDTO
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $name = null,
        public readonly ?string $description = null,
        public readonly ?string $finishDate = null,
        public readonly ?int $status = null,
        public readonly ?string $ownerId = null,
        public readonly ?UserIdCollection $participantIds = null,
        public readonly ?ProjectTaskCollection $tasks = null
    ) {
    }
}
