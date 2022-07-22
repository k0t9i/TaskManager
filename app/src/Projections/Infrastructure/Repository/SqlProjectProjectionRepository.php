<?php
declare(strict_types=1);

namespace App\Projections\Infrastructure\Repository;

use App\Projections\Domain\Entity\ProjectProjection;
use App\Projections\Domain\Repository\ProjectProjectionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SqlProjectProjectionRepository implements ProjectProjectionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function save(ProjectProjection $projection): void
    {
        $this->entityManager->persist($projection);
        $this->entityManager->flush();
    }


    public function findAllById(string $id): array
    {
        return $this->getRepository()->findBy([
            'id' => $id
        ]);
    }

    public function findAllByOwnerId(string $id): array
    {
        return $this->getRepository()->findBy([
            'ownerId' => $id
        ]);
    }

    public function delete(ProjectProjection $projection): void
    {
        $this->entityManager->remove($projection);
        $this->entityManager->flush();
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(ProjectProjection::class);
    }
}
