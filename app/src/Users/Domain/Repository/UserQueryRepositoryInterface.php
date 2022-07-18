<?php
declare(strict_types=1);

namespace App\Users\Domain\Repository;

use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Users\Domain\DTO\UserListResponseDTO;

interface UserQueryRepositoryInterface
{
    /**
     * @return UserListResponseDTO[]
     */
    public function findAllByProjectId(ProjectId $projectId): array;
    public function findByProjectIdAndUserId(ProjectId $projectId, UserId $userId): ?UserListResponseDTO;
}