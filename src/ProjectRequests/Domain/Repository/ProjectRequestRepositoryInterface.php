<?php
declare(strict_types=1);

namespace App\ProjectRequests\Domain\Repository;

use App\ProjectRequests\Domain\Entity\ProjectRequest;
use App\ProjectRequests\Domain\ValueObject\ProjectRequestId;
use App\ProjectRequests\Domain\ValueObject\RequestId;

interface ProjectRequestRepositoryInterface
{
    public function findById(ProjectRequestId $id): ?ProjectRequest;
    public function findByRequestId(RequestId $requestId): ?ProjectRequest;
    public function update(ProjectRequest $project): void;
}