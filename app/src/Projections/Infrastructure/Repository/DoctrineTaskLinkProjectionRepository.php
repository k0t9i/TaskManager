<?php

declare(strict_types=1);

namespace App\Projections\Infrastructure\Repository;

use App\Projections\Domain\Entity\TaskLinkProjection;
use App\Projections\Domain\Repository\TaskLinkProjectionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

final class DoctrineTaskLinkProjectionRepository implements TaskLinkProjectionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function findById(string $id, string $toId): ?TaskLinkProjection
    {
        return $this->getRepository()->findOneBy([
            'id' => $id,
            'toId' => $toId,
        ]);
    }

    public function save(TaskLinkProjection $projection): void
    {
        $this->entityManager->persist($projection);
        $this->entityManager->flush();
    }

    public function delete(TaskLinkProjection $projection): void
    {
        $this->entityManager->remove($projection);
        $this->entityManager->flush();
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(TaskLinkProjection::class);
    }
}
