<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserEmail;
use App\Users\Domain\ValueObject\UserFirstname;
use App\Users\Domain\ValueObject\UserLastname;
use App\Users\Domain\ValueObject\UserPassword;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
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
        $rawUser = $this->queryBuilder()
            ->select('*')
            ->from('users')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchAssociative();
        if ($rawUser === false) {
            return null;
        }

        return $this->find($rawUser);
    }

    /**
     * @param UserEmail $email
     * @return User|null
     * @throws Exception
     */
    public function findByEmail(UserEmail $email): ?User
    {
        $rawUser = $this->queryBuilder()
            ->select('*')
            ->from('users')
            ->where('email = ?')
            ->setParameters([$email->value])
            ->fetchAssociative();
        if ($rawUser === false) {
            return null;
        }

        return $this->find($rawUser);
    }

    private function find(array $rawUser): ?User
    {
        return new User(
            new UserId($rawUser['id']),
            new UserEmail($rawUser['email']),
            new UserFirstname($rawUser['firstname']),
            new UserLastname($rawUser['lastname']),
            new UserPassword($rawUser['password'])
        );
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}