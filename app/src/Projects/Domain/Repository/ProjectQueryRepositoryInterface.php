<?php
declare(strict_types=1);

namespace App\Projects\Domain\Repository;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Shared\Domain\ValueObject\Users\UserId;

interface ProjectQueryRepositoryInterface
{
    /**
     * @param UserId $id
     * @return ProjectListResponseDTO[]
     */
    public function findAllByUserId(UserId $userId): array;
}