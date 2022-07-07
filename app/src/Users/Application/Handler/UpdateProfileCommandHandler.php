<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\Security\AuthenticatorServiceInterface;
use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserLastname;
use App\Users\Application\Command\UpdateProfileCommand;
use App\Users\Application\Service\UserPasswordHasher;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserPassword;
use App\Users\Domain\ValueObject\UserProfile;

final class UpdateProfileCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasher $passwordHasher,
        private readonly AuthenticatorServiceInterface $authenticatorService,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function __invoke(UpdateProfileCommand $command): void
    {
        $userId = $this->authenticatorService->getAuthUser()->getId();
        $user = $this->userRepository->findById($userId);
        if ($user === null) {
            throw new UserNotExistException($userId->value);
        }

        $prevProfile = $user->getProfile();
        $user->changeProfile(
            new UserProfile(
                new UserFirstname($command->firstname ?? $prevProfile->firstname->value),
                new UserLastname($command->lastname ?? $prevProfile->lastname->value),
                $command->password !== null ?
                    $this->passwordHasher->hash(new UserPassword($command->password)) :
                    $prevProfile->password,
                $command->password !== null ?
                    $this->passwordHasher->hash(new UserPassword($command->repeatPassword)) :
                    null,
            )
        );

        $this->userRepository->save($user);
        $this->eventBus->dispatch(...$user->releaseEvents());
    }
}
