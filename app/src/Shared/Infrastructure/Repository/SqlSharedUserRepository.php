<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Shared\Domain\Entity\SharedUser;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\UserEmail;
use App\Shared\Domain\ValueObject\UserFirstname;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\UserLastname;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

final class SqlSharedUserRepository implements SharedUserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    public function getUserTable(): string
    {
        return 'shared_users';
    }

    /**
     * @param UserId $id
     * @return SharedUser|null
     * @throws Exception
     */
    public function findById(UserId $id): ?SharedUser
    {
        $rawUser = $this->queryBuilder()
            ->select('*')
            ->from($this->getUserTable())
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchAssociative();
        if ($rawUser === false) {
            return null;
        }

        return new SharedUser(
            new UserId($rawUser['id']),
            new UserEmail($rawUser['email']),
            new UserFirstname($rawUser['firstname']),
            new UserLastname($rawUser['lastname']),
        );
    }

    /**
     * @param SharedUser $user
     * @throws Exception
     */
    public function save(SharedUser $user): void
    {
        if (!$this->isExist($user->getId())) {
            $this->queryBuilder()
                ->insert($this->getUserTable())
                ->values([
                    'id' => '?',
                    'email' => '?',
                    'firstname' => '?',
                    'lastname' => '?',
                ])
                ->setParameters([
                    $user->getId()->value,
                    $user->getEmail()->value,
                    $user->getFirstname()->value,
                    $user->getLastname()->value,
                ])
                ->executeStatement();
        } else {
            $this->queryBuilder()
                ->update($this->getUserTable())
                ->set('email', '?')
                ->set('firstname', '?')
                ->set('lastname', '?')
                ->where('id = ?')
                ->setParameters([
                    $user->getEmail()->value,
                    $user->getFirstname()->value,
                    $user->getLastname()->value,
                    $user->getId()->value,
                ])
                ->executeStatement();
        }
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
            ->from($this->getUserTable())
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