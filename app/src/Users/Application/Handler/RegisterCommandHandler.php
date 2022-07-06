<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Domain\Bus\Command\CommandHandlerInterface;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Security\PasswordHasherInterface;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\UserEmail;
use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\UserLastname;
use App\Users\Application\Command\RegisterCommand;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\EmailAlreadyTakenException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserPassword;

final class RegisterCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly PasswordHasherInterface $passwordHasher,
        private readonly EventBusInterface $eventBus
    ) {
    }

    public function __invoke(RegisterCommand $command): void
    {
        $user = $this->userRepository->findByEmail(new UserEmail($command->email));
        if ($user !== null) {
            throw new EmailAlreadyTakenException($command->email);
        }

        $hashedPassword = $this->passwordHasher->hashPassword($command->password);
        $newUser = User::create(
            new UserId($this->uuidGenerator->generate()),
            new UserEmail($command->email),
            new UserFirstname($command->firstname),
            new UserLastname($command->lastname),
            new UserPassword($hashedPassword)
        );

        $this->userRepository->save($newUser);
        $this->eventBus->dispatch(...$newUser->releaseEvents());
    }
}
