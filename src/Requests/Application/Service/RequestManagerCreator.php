<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Service\UuidGeneratorInterface;

final class RequestManagerCreator
{
    public function __construct(
        private readonly RequestManagerFactory $managerFactory,
        private readonly UuidGeneratorInterface $uuidGenerator
    ) {
    }

    public function create(string $projectId, int $status, string $ownerId): RequestManager
    {
        $dto = new RequestManagerDTO(
            $this->uuidGenerator->generate(),
            $projectId,
            $status,
            $ownerId,
            new UserIdCollection(),
            new RequestCollection()
        );
        return $this->managerFactory->create($dto);
    }
}
