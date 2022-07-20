<?php
declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Users\Domain\DTO\ProfileResponseDTO;
use App\Users\Domain\DTO\UserResponseDTO;

interface UserQueryRepositoryInterface
{
    /**
     * @return UserResponseDTO[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
    public function findByCriteria(Criteria $criteria): ?UserResponseDTO;
    public function findProfileByCriteria(Criteria $criteria): ?ProfileResponseDTO;
}