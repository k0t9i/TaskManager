<?php
declare(strict_types=1);

namespace App\Users\Domain\Entity;

final class ProfileProjection
{
    public function __construct(
        public readonly string $id,
        public readonly string $firstname,
        public readonly string $lastname,
        public readonly string $email
    ) {
    }
}
