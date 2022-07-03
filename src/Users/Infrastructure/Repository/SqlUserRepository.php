<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserEmail;
use App\Users\Domain\ValueObject\UserFirstname;
use App\Users\Domain\ValueObject\UserLastname;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class SqlUserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param UserId $id
     * @return User|null
     * @throws Exception
     */
    public function findById(UserId $id): ?User
    {
        $rawUser = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('*')
            ->from('users')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchAssociative();
        if ($rawUser === false) {
            return null;
        }

        return new User(
            new UserId($rawUser['id']),
            new UserEmail($rawUser['email']),
            new UserFirstname($rawUser['firstname']),
            new UserLastname($rawUser['lastname'])
        );
    }
}