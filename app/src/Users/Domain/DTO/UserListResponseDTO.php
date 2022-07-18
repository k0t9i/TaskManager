<?php
declare(strict_types=1);

namespace App\Users\Domain\DTO;

final class UserListResponseDTO
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $ownerId,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $email
    ) {
    }

    public static function create(array $item): self
    {
        return new self(
            $item['user_id'],
            $item['owner_id'],
            $item['firstname'],
            $item['lastname'],
            $item['email']
        );
    }
}
