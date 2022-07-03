<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestManagerFactory;

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
