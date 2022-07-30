<?php

declare(strict_types=1);

namespace App\Shared\SharedBoundedContext\Domain\Repository;

use App\Shared\SharedBoundedContext\Domain\Entity\SharedUser;

interface SharedUserRepositoryInterface
{
    public function findById(string $id): ?SharedUser;
    public function save(SharedUser $user): void;
}
