<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Repository;

use App\Projections\Domain\Entity\TaskProjection;
use App\Projections\Domain\Repository\TaskProjectionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineTaskProjectionRepository implements TaskProjectionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function findById(string $id): ?TaskProjection
    {
        return $this->getRepository()->findOneBy([
            'id' => $id,
        ]);
    }

    public function save(TaskProjection $projection): void
    {
        $this->entityManager->persist($projection);
        $this->entityManager->flush();
    }

    public function findAllByOwnerId(string $id): array
    {
        return $this->getRepository()->findBy([
            'ownerId' => $id,
        ]);
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(TaskProjection::class);
    }
}
