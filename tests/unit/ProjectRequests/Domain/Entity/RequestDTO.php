<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\ValueObject\RequestStatus;

class RequestDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly RequestStatus $status,
    ) {
    }
}
