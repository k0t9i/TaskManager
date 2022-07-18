<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Repository;

use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Tasks\Domain\DTO\TaskListResponseDTO;
use App\Tasks\Domain\DTO\TaskResponseDTO;

interface TaskQueryRepositoryInterface
{
    /**
     * @param UserId $id
     * @return TaskListResponseDTO[]
     */
    //TODO by criteria
    public function findAllByProjectIdAndUserId(ProjectId $projectId, UserId $userId): array;
    //TODO by criteria
    public function findByIdAndUserId(TaskId $id, UserId $userId): ?TaskResponseDTO;
    public function findById(TaskId $id): ?TaskResponseDTO;
}