<?php
declare(strict_types=1);

namespace App\Users\Domain\Entity;

final class UserProjection
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $projectId,
        public readonly ?string $ownerId,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $email,
    ) {
    }
}
