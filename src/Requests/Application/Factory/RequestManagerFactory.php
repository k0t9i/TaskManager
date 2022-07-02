<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManagerFactory
{
    public function createRequestManager(RequestManagerDTO $dto) : RequestManager {
        return new RequestManager(
            new RequestManagerId($dto->id),
            new ProjectId($dto->projectId),
            ProjectStatusFactory::objectFromScalar($dto->status),
            new UserId($dto->ownerId),
            new UserIdCollection($dto->participantIds),
            new RequestCollection($dto->requests)
        );
    }
}
