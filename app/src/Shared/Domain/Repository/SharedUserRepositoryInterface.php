<?php
declare(strict_types=1);

namespace App\Shared\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Entity\SharedUser;
use App\Shared\Domain\ValueObject\Users\UserId;

interface SharedUserRepositoryInterface
{
    public function findById(UserId $id): ?SharedUser;
    public function findByCriteria(Criteria $criteria): ?SharedUser;
    public function save(SharedUser $user): void;
}