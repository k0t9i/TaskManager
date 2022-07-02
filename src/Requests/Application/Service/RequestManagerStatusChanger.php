<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Application\Factory\RequestManagerDTO;
use App\Requests\Application\Factory\RequestManagerFactory;
use App\Requests\Domain\Entity\RequestManager;

final class RequestManagerStatusChanger
{
    public function __construct(private readonly RequestManagerFactory $managerFactory)
    {
    }

    public function changeStatus(RequestManager $requestManager, int $status): RequestManager
    {
        $dto = new RequestManagerDTO(
            $requestManager->getId()->value,
            $requestManager->getProjectId()->value,
            $status,
            $requestManager->getOwnerId()->value,
            $requestManager->getParticipantIds(),
            $requestManager->getRequests()
        );
        return $this->managerFactory->create($dto);
    }
}
