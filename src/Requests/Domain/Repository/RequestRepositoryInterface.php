<?php
declare(strict_types=1);

namespace App\Requests\Domain\Repository;

use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\ValueObject\ProjectId;

interface RequestRepositoryInterface
{
    public function findById(RequestId $id): ?Request;
    public function save(Request $project): void;

    /**
     * @param ProjectId $projectId
     * @return array|Request[]
     */
    public function findAllByProjectId(ProjectId $projectId): array;
}