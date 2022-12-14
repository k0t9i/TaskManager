<?php

declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Application\Bus\Command\CommandHandlerInterface;
use App\Shared\Application\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserFirstname;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Domain\ValueObject\Users\UserLastname;
use App\Users\Application\Command\RegisterCommand;
use App\Users\Application\Service\UserPasswordHasher;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\EmailAlreadyTakenException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserPassword;
use App\Users\Domain\ValueObject\UserProfile;

final class RegisterCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasher $passwordHasher,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function __invoke(RegisterCommand $command): void
    {
        $user = $this->userRepository->findByEmail(new UserEmail($command->email));
        if (null !== $user) {
            throw new EmailAlreadyTakenException($command->email);
        }

        $newUser = User::create(
            new UserId($command->id),
            new UserEmail($command->email),
            new UserProfile(
                new UserFirstname($command->firstname),
                new UserLastname($command->lastname),
                $this->passwordHasher->hash(new UserPassword($command->password)),
                $this->passwordHasher->hash(new UserPassword($command->repeatPassword)),
            )
        );

        $this->userRepository->save($newUser);
        $this->eventBus->dispatch(...$newUser->releaseEvents());
    }
}
