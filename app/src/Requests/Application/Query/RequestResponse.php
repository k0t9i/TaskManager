<?php
declare(strict_types=1);

namespace App\Requests\Application\Query;

use App\Requests\Domain\Entity\Request;

final class RequestResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $changeDate,
        public readonly string $userId,
        public readonly int $status
    ) {
    }

    public static function createFromEntity(Request $request): self
    {
        return new self(
            $request->getId()->value,
            $request->getChangeDate()->getValue(),
            $request->getUserId()->value,
            $request->getStatus()->getScalar()
        );
    }
}
