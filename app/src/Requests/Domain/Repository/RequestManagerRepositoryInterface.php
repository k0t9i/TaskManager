<?php
declare(strict_types=1);

namespace App\Requests\Domain\Repository;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\ValueObject\Projects\ProjectId;

interface RequestManagerRepositoryInterface
{
    public function findByProjectId(ProjectId $id): ?RequestManager;
    public function findByRequestId(RequestId $id): ?RequestManager;
    public function save(RequestManager $manager): void;
}