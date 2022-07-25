<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Entity\ProjectListProjection;
use App\Projects\Domain\Entity\ProjectProjection;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Shared\Application\Service\CriteriaFieldValidatorInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Service\CriteriaToDoctrineCriteriaConverterInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

class SqlProjectQueryRepository implements ProjectQueryRepositoryInterface
{
    private const MANAGER = 'read';

    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly CriteriaToDoctrineCriteriaConverterInterface $converter,
        private readonly CriteriaFieldValidatorInterface $validator
    ) {
    }

    /**
     * @param Criteria $criteria
     * @return ProjectListProjection[]
     * @throws QueryException
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        $this->validator->validate($criteria, ProjectListProjection::class);

        return $this->getListRepository()
            ->createQueryBuilder('t')
            ->addCriteria($this->converter->convert($criteria))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Criteria $criteria
     * @return int
     * @throws QueryException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        $this->validator->validate($criteria, ProjectListProjection::class);

        $doctrineCriteria = $this->converter->convert($criteria);
        $doctrineCriteria->setFirstResult(null);
        $doctrineCriteria->setMaxResults(null);
        return $this->getRepository()
            ->createQueryBuilder('t')
            ->select('count(t.id)')
            ->addCriteria($doctrineCriteria)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Criteria $criteria
     * @return ProjectProjection|null
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function findByCriteria(Criteria $criteria): ?ProjectProjection
    {
        $this->validator->validate($criteria, ProjectProjection::class);

        return $this->getRepository()
            ->createQueryBuilder('t')
            ->addCriteria($this->converter->convert($criteria))
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getListRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            ProjectListProjection::class,
            self::MANAGER
        );
    }

    private function getRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            ProjectProjection::class,
            self::MANAGER
        );
    }
}