<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Service\DoctrineOptimisticLockTrait;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Infrastructure\Persistence\Doctrine\Proxy\TaskManagerProxy;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

final class DoctrineTaskManagerRepository implements TaskManagerRepositoryInterface
{
    use DoctrineOptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param ProjectId $id
     * @return TaskManager|null
     */
    public function findByProjectId(ProjectId $id): ?TaskManager
    {
        /** @var TaskManagerProxy $proxy */
        $proxy = $this->getRepository()->findOneBy([
            'projectId' => $id->value
        ]);

        return $proxy?->createEntity();
    }

    /**
     * @param TaskId $id
     * @return TaskManager|null
     * @throws NonUniqueResultException
     */
    public function findByTaskId(TaskId $id): ?TaskManager
    {
        /** @var TaskManagerProxy $proxy */
        $proxy = $this->getRepository()
            ->createQueryBuilder('t')
            ->leftJoin('t.tasks', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id->value)
            ->getQuery()
            ->getOneOrNullResult();

        return $proxy?->createEntity();
    }

    /**
     * @param TaskManager $manager
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(TaskManager $manager): void
    {
        $proxy = $this->getOrCreate($manager);

        $this->lock($this->entityManager, $proxy);

        $proxy->refresh();

        //FIXME bump version if a child was changed
        $this->entityManager->persist($proxy);
        $this->entityManager->flush();
    }

    private function getOrCreate(TaskManager $manager): TaskManagerProxy
    {
        $result = $this->getRepository()->findOneBy([
            'id' => $manager->getId()->value
        ]);
        if ($result === null) {
            $result = new TaskManagerProxy($manager);
        }
        return $result;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(TaskManagerProxy::class);
    }
}
