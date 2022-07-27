<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Infrastructure\Persistence\Doctrine\Proxy\ProjectProxy;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Service\DoctrineOptimisticLockTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class DoctrineProjectRepository implements ProjectRepositoryInterface
{
    use DoctrineOptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PersistentCollectionLoaderInterface $collectionLoader
    ) {
    }

    /**
     * @param ProjectId $id
     * @return Project|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?Project
    {
        /** @var ProjectProxy $proxy */
        $proxy = $this->getRepository()->findOneBy([
            'id' => $id->value
        ]);

        return $proxy?->createEntity();
    }

    /**
     * @param Project $project
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(Project $project): void
    {
        $proxy = $this->getOrCreate($project);

        $this->lock($this->entityManager, $proxy);

        $proxy->refresh($this->collectionLoader);

        $this->entityManager->persist($proxy);
        $this->entityManager->flush();
    }

    private function getOrCreate(Project $project): ProjectProxy
    {
        $result = $this->getRepository()->findOneBy([
            'id' => $project->getId()->value
        ]);
        if ($result === null) {
            $result = new ProjectProxy($project);
        }
        return $result;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(ProjectProxy::class);
    }
}