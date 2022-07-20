<?php
declare(strict_types=1);

namespace App\Shared\SharedBoundedContext\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\SharedBoundedContext\Domain\Entity\SharedUser;

interface SharedUserRepositoryInterface
{
    public function findById(UserId $id): ?SharedUser;
    public function findByCriteria(Criteria $criteria): ?SharedUser;
    public function save(SharedUser $user): void;
}