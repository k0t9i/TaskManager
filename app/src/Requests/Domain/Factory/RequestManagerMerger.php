<?php
declare(strict_types=1);

namespace App\Requests\Domain\Factory;

use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\DTO\RequestManagerMergeDTO;
use App\Requests\Domain\Entity\RequestManager;

final class RequestManagerMerger
{
    public function __construct(private readonly RequestManagerFactory $factory)
    {
    }

    public function merge(RequestManager $source, RequestManagerMergeDTO $dto) : RequestManager
    {
        return $this->factory->create(new RequestManagerDTO(
            $dto->id ?? $source->getId()->value,
            $dto->projectId ?? $source->getProjectId()->value,
            $dto->status ?? $source->getStatus()->getScalar(),
            $dto->ownerId ?? $source->getOwner()->userId->value,
            $dto->participantIds ?? $source->getParticipants()->getInnerItems(),
            $dto->requests ?? $source->getRequests()->getInnerItems(),
        ));
    }
}
