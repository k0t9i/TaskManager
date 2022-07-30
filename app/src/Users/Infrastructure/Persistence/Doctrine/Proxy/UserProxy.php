<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use App\Users\Domain\Entity\User;

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

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getPassword(): string
    {
        return $this->password;
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

    public function changeEntity(User $entity): void
    {
        $this->entity = $entity;
    }
}
