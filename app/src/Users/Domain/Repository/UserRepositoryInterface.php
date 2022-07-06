<?php
declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Shared\Domain\ValueObject\UserEmail;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;
    public function findByEmail(UserEmail $email): ?User;
    public function save(User $user): void;
}