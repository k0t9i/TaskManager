<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestStatusFactory;

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
            RequestStatusFactory::scalarFromObject($requestManager->getStatus()),
            $ownerId,
            $requestManager->getParticipantIds()->getItems(),
            $requestManager->getRequests()->getItems()
        );
        return $this->managerFactory->createRequestManager($dto);
    }
}