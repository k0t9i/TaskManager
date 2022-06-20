<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Users\Domain\Entity\User;
use App\Users\Domain\Exception\UserNotExistException;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Domain\ValueObject\UserId;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function findById(UserId $id): ?User
    {
        return $this->repository()->find($id);
    }

    public function getById(UserId $id): User
    {
        $user = $this->findById($id);
        if ($user === null) {
            throw new UserNotExistException();
        }
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        // TODO: Implement findAll() method.
        return [];
    }

    private function repository(): EntityRepository
    {
        return $this->entityManager->getRepository(User::class);
    }
}