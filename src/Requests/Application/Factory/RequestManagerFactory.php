<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

use App\Requests\Application\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManagerFactory
{
    public function create(RequestManagerDTO $dto) : RequestManager
    {
        return new RequestManager(
            new RequestManagerId($dto->id),
            new ProjectId($dto->projectId),
            ProjectStatusFactory::objectFromScalar($dto->status),
            new UserId($dto->ownerId),
            $dto->participantIds,
            $dto->requests
        );
    }
}
