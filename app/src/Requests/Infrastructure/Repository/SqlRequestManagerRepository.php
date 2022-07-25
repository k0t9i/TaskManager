<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Infrastructure\Persistence\Doctrine\Proxy\RequestManagerProxy;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Service\DoctrineOptimisticLockTrait;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;

final class SqlRequestManagerRepository implements RequestManagerRepositoryInterface
{
    use DoctrineOptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param ProjectId $id
     * @return RequestManager|null
     * @throws Exception
     */
    public function findByProjectId(ProjectId $id): ?RequestManager
    {
        /** @var RequestManagerProxy $proxy */
        $proxy = $this->getRepository()->findOneBy([
            'projectId' => $id->value
        ]);

        return $proxy?->createEntity();
    }

    /**
     * @param RequestId $id
     * @return RequestManager|null
     * @throws Exception
     * @throws NonUniqueResultException
     */
    public function findByRequestId(RequestId $id): ?RequestManager
    {
        /** @var RequestManagerProxy $proxy */
        $proxy = $this->getRepository()
            ->createQueryBuilder('t')
            ->leftJoin('t.requests', 'r')
            ->where('r.id = :id')
            ->setParameter('id', $id->value)
            ->getQuery()
            ->getOneOrNullResult();

        return $proxy?->createEntity();
    }

    /**
     * @param RequestManager $manager
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(RequestManager $manager): void
    {
        $proxy = $this->getOrCreate($manager);

        $this->lock($this->entityManager, $proxy);

        $proxy->refresh();

        //FIXME bump version if a child was changed
        $this->entityManager->persist($proxy);
        $this->entityManager->flush();
    }

    private function getOrCreate(RequestManager $manager): RequestManagerProxy
    {
        $result = $this->getRepository()->findOneBy([
            'id' => $manager->getId()->value
        ]);
        if ($result === null) {
            $result = new RequestManagerProxy($manager);
        }
        return $result;
    }

    private function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(RequestManagerProxy::class);
    }
}
