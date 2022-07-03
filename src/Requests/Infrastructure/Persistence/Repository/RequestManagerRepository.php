<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Repository;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Shared\Domain\ValueObject\ProjectId;

class RequestManagerRepository implements RequestManagerRepositoryInterface
{

    public function findById(RequestManagerId $id): ?RequestManager
    {
        // TODO: Implement findById() method.
        return null;
    }

    public function findByProjectId(ProjectId $id): ?RequestManager
    {
        // TODO: Implement findByProjectId() method.
        return null;
    }

    public function findByRequestId(RequestId $id): ?RequestManager
    {
        // TODO: Implement findByRequestId() method.
        return null;
    }

    public function save(RequestManager $manager): void
    {
        // TODO: Implement save() method.
    }
}
