<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

final class RequestManagerDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $projectId,
        public readonly int $status,
        public readonly string $ownerId,
        public readonly array $participantIds = [],
        public readonly array $requests = []
    ) {
    }
}
