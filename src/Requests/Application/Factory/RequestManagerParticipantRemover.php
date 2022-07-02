<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestStatusFactory;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManagerParticipantRemover
{
    public function __construct(private readonly RequestManagerFactory $managerFactory)
    {
    }

    public function removeParticipant(RequestManager $requestManager, string $participantId): RequestManager
    {
        $dto = new RequestManagerDTO(
            $requestManager->getId()->value,
            $requestManager->getProjectId()->value,
            RequestStatusFactory::scalarFromObject($requestManager->getStatus()),
            $requestManager->getOwnerId()->value,
            $requestManager->getParticipantIds()->getItems(),
            $requestManager->getRequests()->getItems()
        );
        $manager = $this->managerFactory->createRequestManager($dto);
        $manager->getParticipantIds()->remove(new UserId($participantId));
        return $manager;
    }
}
