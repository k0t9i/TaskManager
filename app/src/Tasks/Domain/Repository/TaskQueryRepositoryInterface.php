<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Tasks\Domain\DTO\TaskListResponseDTO;
use App\Tasks\Domain\DTO\TaskResponseDTO;

interface TaskQueryRepositoryInterface
{
    /**
     * @return TaskListResponseDTO[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
    public function findByCriteria(Criteria $criteria): ?TaskResponseDTO;
}