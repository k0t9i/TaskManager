<?php
declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Users\Domain\Entity\User;
use App\Users\Domain\ValueObject\UserId;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;
    public function getById(UserId $id): User;

    /**
     * @return User[]
     */
    public function findAll(): array;
}