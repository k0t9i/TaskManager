<?php
declare(strict_types=1);

namespace App\Projections\Infrastructure\Repository;

use App\Projections\Domain\Entity\RequestProjection;
use App\Projections\Domain\Repository\RequestProjectionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class SqlRequestProjectionRepository implements RequestProjectionRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function findById(string $id): ?RequestProjection
    {
        return $this->getRepository()->findOneBy([
            'id' => $id
        ]);
    }

    public function findByUserId(string $id): array
    {
        return $this->getRepository()->findBy([
            'userId' => $id
        ]);
    }

    public function save(RequestProjection $projection): void
    {
        $this->entityManager->persist($projection);
        $this->entityManager->flush();
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(RequestProjection::class);
    }
}
