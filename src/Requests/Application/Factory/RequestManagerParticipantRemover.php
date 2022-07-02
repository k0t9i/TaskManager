<?php
declare(strict_types=1);

namespace App\Requests\Application\Factory;

use App\Requests\Domain\Entity\RequestManager;
use App\Shared\Domain\ValueObject\UserId;

final class RequestManagerParticipantRemover
{
    public function removeParticipant(RequestManager $requestManager, string $participantId): RequestManager
    {
        $requestManager->getParticipantIds()->remove(new UserId($participantId));
        return $requestManager;
    }
}
