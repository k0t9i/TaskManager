<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

use App\Requests\Domain\Entity\RequestManager;
use App\Shared\Domain\UuidGeneratorInterface;

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
            $ownerId
        );
        return $this->managerFactory->createRequestManager($dto);
    }
}
