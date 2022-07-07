<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\UserEmail;
use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\UserLastname;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserPassword;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlUserRepository implements UserRepositoryInterface
{
    use OptimisticLockTrait;

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
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        $version = $this->getVersion($user->getId());
        $isExist = $version > 0;
        $this->ensureIsVersionLesserThanPrevious($user->getId()->value, $version);
        $version += 1;

        if ($isExist) {
            $this->updateUser($user, $version);
        } else {
            $this->insertUser($user, $version);
        }
    }

    private function find(array $rawUser): ?User
    {
        $this->saveVersion($rawUser['id'], $rawUser['version']);

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
     * @return int
     * @throws Exception
     */
    private function getVersion(UserId $id): int
    {
        $version = $this->queryBuilder()
            ->select('version')
            ->from('users')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchOne();
        return $version ?: 0;
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }

    /**
     * @param User $user
     * @param int $version
     * @throws Exception
     */
    private function updateUser(User $user, int $version): void
    {
        $this->queryBuilder()
            ->update('users')
            ->set('email', '?')
            ->set('firstname', '?')
            ->set('lastname', '?')
            ->set('password', '?')
            ->set('version', '?')
            ->where('id = ?')
            ->setParameters([
                $user->getEmail()->value,
                $user->getFirstname()->value,
                $user->getLastname()->value,
                $user->getPassword()->value,
                $version,
                $user->getId()->value,
            ])
            ->executeStatement();
    }

    /**
     * @param User $user
     * @param int $version
     * @throws Exception
     */
    private function insertUser(User $user, int $version): void
    {
        $this->queryBuilder()
            ->insert('users')
            ->values([
                'id' => '?',
                'email' => '?',
                'firstname' => '?',
                'lastname' => '?',
                'password' => '?',
                'version' => '?'
            ])
            ->setParameters([
                $user->getId()->value,
                $user->getEmail()->value,
                $user->getFirstname()->value,
                $user->getLastname()->value,
                $user->getPassword()->value,
                $version
            ])
            ->executeStatement();
    }
}