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

    /**
     * @param User $user
     * @throws Exception
     */
    public function save(User $user): void
    {
        if (!$this->isExist($user->getId())) {
            $this->queryBuilder()
                ->insert('users')
                ->values([
                    'id' => '?',
                    'email' => '?',
                    'firstname' => '?',
                    'lastname' => '?',
                    'password' => '?'
                ])
                ->setParameters([
                    $user->getId()->value,
                    $user->getEmail()->value,
                    $user->getFirstname()->value,
                    $user->getLastname()->value,
                    $user->getPassword()->value,
                ])
                ->executeStatement();
        } else {
            $this->queryBuilder()
                ->update('users')
                ->set('email', '?')
                ->set('firstname', '?')
                ->set('lastname', '?')
                ->set('password', '?')
                ->where('id = ?')
                ->setParameters([
                    $user->getEmail()->value,
                    $user->getFirstname()->value,
                    $user->getLastname()->value,
                    $user->getPassword()->value,
                    $user->getId()->value,
                ])
                ->executeStatement();
        }
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

    /**
     * @param UserId $id
     * @return bool
     * @throws Exception
     */
    private function isExist(UserId $id): bool
    {
        $count = $this->queryBuilder()
            ->select('count(id)')
            ->from('users')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchOne();
        return $count > 0;
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}