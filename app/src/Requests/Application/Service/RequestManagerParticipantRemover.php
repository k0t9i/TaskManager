<?php
declare(strict_types=1);

namespace App\Requests\Application\Service;

use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManagerParticipantRemover
{
    public function __construct(private readonly RequestManagerFactory $managerFactory)
    {
    }

    public function removeParticipant(RequestManager $requestManager, string $participantId): RequestManager
    {
        $participants = $requestManager->getParticipantIds()->remove(new UserId($participantId));
        $dto = new RequestManagerDTO(
            $requestManager->getId()->value,
            $requestManager->getProjectId()->value,
            ProjectStatusFactory::scalarFromObject($requestManager->getStatus()),
            $requestManager->getOwnerId()->value,
            $participants,
            $requestManager->getRequests()
        );

        return $this->managerFactory->create($dto);
    }
}