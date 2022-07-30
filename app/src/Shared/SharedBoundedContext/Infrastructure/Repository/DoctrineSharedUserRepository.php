<?php

declare(strict_types=1);

namespace App\Shared\SharedBoundedContext\Infrastructure\Repository;

use App\Shared\SharedBoundedContext\Domain\Entity\SharedUser;
use App\Shared\SharedBoundedContext\Domain\Repository\SharedUserRepositoryInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineSharedUserRepository implements SharedUserRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws Exception
     */
    public function findById(string $id): ?SharedUser
    {
        return $this->getRepository()->findOneBy([
            'id' => $id,
        ]);
    }

    /**
     * @throws Exception
     */
    public function save(SharedUser $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(SharedUser::class);
    }
}
