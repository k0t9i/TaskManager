<?php
declare(strict_types=1);

namespace App\Requests\Domain\Factory;

use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Requests\Domain\ValueObject\Requests;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use App\Shared\Domain\ValueObject\Users\UserId;

final class RequestManagerFactory
{
    public function create(RequestManagerDTO $dto) : RequestManager
    {
        return new RequestManager(
            new RequestManagerId($dto->id),
            new ProjectId($dto->projectId),
            ProjectStatus::createFromScalar($dto->status),
            new Owner(new UserId($dto->ownerId)),
            new Participants($dto->participantIds),
            new Requests($dto->requests)
        );
    }
}
