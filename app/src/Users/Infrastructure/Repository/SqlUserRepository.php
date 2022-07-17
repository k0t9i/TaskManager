<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserFirstname;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Domain\ValueObject\Users\UserLastname;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Shared\Infrastructure\Persistence\StorageSaverInterface;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserPassword;
use App\Users\Domain\ValueObject\UserProfile;
use App\Users\Infrastructure\Persistence\Hydrator\Metadata\UserStorageMetadata;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlUserRepository implements UserRepositoryInterface
{
    use OptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageSaverInterface $storageSaver
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
        $prevVersion = $this->getVersion($user->getId()->value);

        $metadata = new UserStorageMetadata();
        if ($prevVersion > 0) {
            $this->storageSaver->update($user, $metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($user, $metadata);
        }
    }

    private function find(array $rawUser): ?User
    {
        $this->setVersion($rawUser['id'], $rawUser['version']);

        return new User(
            new UserId($rawUser['id']),
            new UserEmail($rawUser['email']),
            new UserProfile(
                new UserFirstname($rawUser['firstname']),
                new UserLastname($rawUser['lastname']),
                new UserPassword($rawUser['password'])
            )
        );
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}