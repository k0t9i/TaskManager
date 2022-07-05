<?php
declare(strict_types=1);

namespace App\Requests\Domain\DTO;

final class RequestDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $userId,
        public readonly string $userEmail,
        public readonly int $status,
        public readonly string $changeDate
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['id'],
            $item['user_id'],
            $item['user_email'],
            $item['status'],
            $item['change_date']
        );
    }
}
