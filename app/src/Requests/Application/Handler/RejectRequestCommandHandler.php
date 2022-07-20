<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Command\RejectRequestCommand;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\Requests\RejectedRequestStatus;

final class RejectRequestCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(RejectRequestCommand $command): void
    {
        $requestId = new RequestId($command->id);
        $manager = $this->managerRepository->findByRequestId($requestId);
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $manager->changeRequestStatus(
            $requestId,
            new RejectedRequestStatus(),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}