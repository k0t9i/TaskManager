<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function findById(UserId $id): ?User
    {
        // TODO: Implement findById() method.
        return null;
    }
}