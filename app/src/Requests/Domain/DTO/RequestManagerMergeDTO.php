<?php
declare(strict_types=1);

namespace App\Requests\Domain\DTO;

use App\Requests\Domain\Collection\RequestCollection;
use App\Shared\Domain\Collection\UserIdCollection;

final class RequestManagerMergeDTO
{
    public function __construct(
        public readonly ?string $id = null,
        public readonly ?string $projectId = null,
        public readonly ?int $status = null,
        public readonly ?string $ownerId = null,
        public readonly ?UserIdCollection $participantIds = null,
        public readonly ?RequestCollection $requests = null
    ) {
    }
}
