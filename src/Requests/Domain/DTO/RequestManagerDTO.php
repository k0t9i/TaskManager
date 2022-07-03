<?php
declare(strict_types=1);

namespace App\Requests\Domain\DTO;

use App\Requests\Domain\Collection\RequestCollection;
use App\Shared\Domain\Collection\UserIdCollection;

final class RequestManagerDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly int $status,
        public readonly string $ownerId,
        public readonly UserIdCollection $participantIds,
        public readonly RequestCollection $requests
    ) {
    }

    public static function createFromRequest(array $item): self
    {
        return new self(
            $item['id'],
            $item['project_id'],
            $item['status'],
            $item['owner_id'],
            $item['participant_ids'],
            $item['requests']
        );
    }
}
