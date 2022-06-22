<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Repository;

use App\ProjectRequests\Domain\Entity\ProjectRequest;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Projects\Domain\ValueObject\ProjectId;

interface ProjectRequestRepositoryInterface
{
    public function findById(ProjectId $id): ?ProjectRequest;
    public function getById(ProjectId $id): ProjectRequest;
    public function findByRequestId(RequestId $requestId): ?ProjectRequest;
    public function update(ProjectRequest $project): void;
}