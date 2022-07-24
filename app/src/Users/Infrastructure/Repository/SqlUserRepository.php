<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Service\DoctrineOptimisticLockTrait;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Infrastructure\Persistence\Doctrine\Proxy\UserProxy;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class SqlUserRepository implements UserRepositoryInterface
{
    use DoctrineOptimisticLockTrait;

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
        /** @var UserProxy $proxy */
        $proxy = $this->getRepository()->findOneBy([
            'id' => $id->value
        ]);

        return $proxy?->createEntity();
    }

    /**
     * @param UserEmail $email
     * @return User|null
     * @throws Exception
     */
    public function findByEmail(UserEmail $email): ?User
    {
        /** @var UserProxy $proxy */
        $proxy = $this->getRepository()->findOneBy([
            'email' => $email->value
        ]);

        return $proxy?->createEntity();
    }

    /**
     * @param User $user
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        /** @var UserProxy $proxy */
        $proxy = $this->getOrCreate($user->getId()->value);

        $this->lock($this->entityManager, $proxy);

        $proxy->loadFromEntity($user);

        //FIXME bump version if a child was changed
        $this->entityManager->persist($proxy);
        $this->entityManager->flush();
    }

    private function getOrCreate(string $id): UserProxy
    {
        $result = $this->getRepository()->findOneBy([
            'id' => $id
        ]);
        if ($result === null) {
            $result = new UserProxy();
        }
        return $result;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(UserProxy::class);
    }
}