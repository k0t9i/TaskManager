<?php
declare(strict_types=1);

namespace App\Users\Application\Service;

use App\Shared\Application\Service\AuthenticatorServiceInterface;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserPassword;

final class LoginService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserPasswordHasher $hasher,
        private readonly AuthenticatorServiceInterface $authenticator
    ) {
    }

    public function login(string $username, string $plainPassword): string
    {
        $user = $this->userRepository->findByEmail(new UserEmail($username));
        if ($user === null) {
            throw new UserNotExistException($username);
        }

        if (!$this->hasher->verify($user->getProfile()->password, new UserPassword($plainPassword))) {
            throw new UserNotExistException($username);
        }

        return $this->authenticator->getToken($user->getId()->value);
    }
}
