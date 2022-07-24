<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserFirstname;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Domain\ValueObject\Users\UserLastname;
use App\Shared\Infrastructure\Service\DoctrineVersionedProxyInterface;
use App\Users\Domain\Entity\User;
use App\Users\Domain\ValueObject\UserPassword;
use App\Users\Domain\ValueObject\UserProfile;

final class UserProxy implements DoctrineVersionedProxyInterface
{
    private string $id;
    private string $email;
    private string $firstname;
    private string $lastname;
    private string $password;
    private int $version;

    public function getVersion(): int
    {
        return $this->version;
    }

    public function loadFromEntity(User $entity): void
    {
        $this->id = $entity->getId()->value;
        $this->email = $entity->getEmail()->value;
        $this->firstname = $entity->getProfile()->firstname->value;
        $this->lastname = $entity->getProfile()->lastname->value;
        $this->password = $entity->getProfile()->password->value;
    }

    public function createEntity(): User
    {
        return new User(
            new UserId($this->id),
            new UserEmail($this->email),
            new UserProfile(
                new UserFirstname($this->firstname),
                new UserLastname($this->lastname),
                new UserPassword($this->password)
            )
        );
    }
}
