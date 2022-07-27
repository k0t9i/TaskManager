<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserFirstname;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Domain\ValueObject\Users\UserLastname;
use App\Users\Domain\Entity\User;
use App\Users\Domain\ValueObject\UserPassword;
use App\Users\Domain\ValueObject\UserProfile;

final class UserProxyFactory
{
    public function createEntity(?UserProxy $proxy): ?User
    {
        if ($proxy === null) {
            return null;
        }

        $entity = new User(
            new UserId($proxy->getId()),
            new UserEmail($proxy->getEmail()),
            new UserProfile(
                new UserFirstname($proxy->getFirstname()),
                new UserLastname($proxy->getLastname()),
                new UserPassword($proxy->getPassword())
            )
        );

        $proxy->changeEntity($entity);

        return $entity;
    }
}
