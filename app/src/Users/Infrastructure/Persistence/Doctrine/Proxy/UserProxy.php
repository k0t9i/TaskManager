<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserFirstname;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Domain\ValueObject\Users\UserLastname;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use App\Users\Domain\Entity\User;
use App\Users\Domain\ValueObject\UserPassword;
use App\Users\Domain\ValueObject\UserProfile;

final class UserProxy implements DoctrineVersionedProxyInterface, DoctrineProxyInterface
{
    private string $id;
    private string $email;
    private string $firstname;
    private string $lastname;
    private string $password;
    private int $version;
    private ?User $entity = null;

    public function __construct(User $entity)
    {
        $this->entity = $entity;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->id = $this->entity->getId()->value;
        $this->email = $this->entity->getEmail()->value;
        $this->firstname = $this->entity->getProfile()->firstname->value;
        $this->lastname = $this->entity->getProfile()->lastname->value;
        $this->password = $this->entity->getProfile()->password->value;
    }

    public function createEntity(): User
    {
        if ($this->entity === null) {
            $this->entity = new User(
                new UserId($this->id),
                new UserEmail($this->email),
                new UserProfile(
                    new UserFirstname($this->firstname),
                    new UserLastname($this->lastname),
                    new UserPassword($this->password)
                )
            );
        }
        return $this->entity;
    }
}
