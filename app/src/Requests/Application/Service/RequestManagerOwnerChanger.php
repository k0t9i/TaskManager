<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Shared\Domain\Factory\ProjectStatusFactory;

final class RequestManagerOwnerChanger
{
    public function __construct(private readonly RequestManagerFactory $managerFactory)
    {
    }

    public function changeOwner(RequestManager $requestManager, string $ownerId): RequestManager
    {
        $dto = new RequestManagerDTO(
            $requestManager->getId()->value,
            $requestManager->getProjectId()->value,
            ProjectStatusFactory::scalarFromObject($requestManager->getStatus()),
            $ownerId,
            $requestManager->getParticipantIds(),
            $requestManager->getRequests()
        );
        return $this->managerFactory->create($dto);
    }
}
