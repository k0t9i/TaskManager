<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Repository;

use App\Projections\Domain\Entity\UserProjection;
use App\Projections\Domain\Repository\UserProjectionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineUserProjectionRepository implements UserProjectionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function findByUserId(string $id): ?UserProjection
    {
        return $this->getRepository()->findOneBy([
            'userId' => $id,
        ]);
    }

    public function findByUserIdAndProjectId(string $id, string $projectId): ?UserProjection
    {
        return $this->getRepository()->findOneBy([
            'userId' => $id,
            'projectId' => $projectId,
        ]);
    }

    public function save(UserProjection $projection): void
    {
        $this->entityManager->persist($projection);
        $this->entityManager->flush();
    }

    /**
     * @return UserProjection[]
     */
    public function findAllByUserId(string $id): array
    {
        return $this->getRepository()->findBy([
            'userId' => $id,
        ]);
    }

    /**
     * @return UserProjection[]
     */
    public function findAllByProjectId(string $id): array
    {
        return $this->getRepository()->findBy([
            'projectId' => $id,
        ]);
    }

    public function delete(UserProjection $projection): void
    {
        $this->entityManager->remove($projection);
        $this->entityManager->flush();
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(UserProjection::class);
    }
}
