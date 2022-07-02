<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

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
            $requestManager->getParticipantIds()->getItems(),
            $requestManager->getRequests()->getItems()
        );
        return $this->managerFactory->createRequestManager($dto);
    }
}
