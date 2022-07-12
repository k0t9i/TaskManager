<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Projects\Domain\DTO\ProjectResponseDTO;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;

interface ProjectQueryRepositoryInterface
{
    /**
     * @param UserId $id
     * @return ProjectListResponseDTO[]
     */
    public function findAllByUserId(UserId $userId): array;
    public function findByIdAndUserId(ProjectId $id, UserId $userId): ?ProjectResponseDTO;
}