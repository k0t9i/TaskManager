<?php
declare(strict_types=1);

namespace App\Requests\Application\Handler;

use App\Requests\Application\Command\ConfirmRequestCommand;
use App\Requests\Domain\Exception\RequestManagerNotExistsException;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\ConfirmedRequestStatus;

final class ConfirmRequestCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly RequestManagerRepositoryInterface $managerRepository,
        private readonly EventBusInterface $eventBus,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function __invoke(ConfirmRequestCommand $command): void
    {
        $requestId = new RequestId($command->id);
        $manager = $this->managerRepository->findByRequestId($requestId);
        if ($manager === null) {
            throw new RequestManagerNotExistsException();
        }

        $manager->changeRequestStatus(
            $requestId,
            new ConfirmedRequestStatus(),
            $this->authenticator->getAuthUser()->getId()
        );

        $this->managerRepository->save($manager);
        $this->eventBus->dispatch(...$manager->releaseEvents());
    }
}