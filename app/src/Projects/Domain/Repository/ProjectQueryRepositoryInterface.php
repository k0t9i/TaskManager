<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Projects\Domain\DTO\ProjectResponseDTO;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;

interface ProjectQueryRepositoryInterface
{
    public function findByIdAndUserId(ProjectId $id, UserId $userId): ?ProjectResponseDTO;
    public function findById(ProjectId $id): ?ProjectResponseDTO;
    /**
     * @return ProjectListResponseDTO[]
     */
    public function findAllByCriteria(Criteria $criteria): array;
    public function findCountByCriteria(Criteria $criteria): int;
}