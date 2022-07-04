<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Users\Domain\Entity\User;

final class GetUserQueryResponse implements QueryResponseInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $firstname,
        public readonly string $lastname
    ) {
    }

    public static function createFromEntity(User $user): self
    {
        return new self(
            $user->getId()->value,
            $user->getEmail()->value,
            $user->getFirstname()->value,
            $user->getLastname()->value
        );
    }
}
