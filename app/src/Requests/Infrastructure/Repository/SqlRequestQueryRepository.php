<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Entity\RequestListProjection;
use App\Requests\Domain\Repository\RequestQueryRepositoryInterface;
use App\Shared\Application\Service\CriteriaFieldValidatorInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Service\CriteriaToDoctrineCriteriaConverterInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use ReflectionException;

class SqlRequestQueryRepository implements RequestQueryRepositoryInterface
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
     * @return array
     * @throws QueryException
     * @throws ReflectionException
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        $this->validator->validate($criteria, RequestListProjection::class);

        return $this->getRepository()
            ->createQueryBuilder('t')
            ->addCriteria($this->converter->convert($criteria))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param Criteria $criteria
     * @return int
     * @throws QueryException
     * @throws ReflectionException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        $this->validator->validate($criteria, RequestListProjection::class);

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

    private function getRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            RequestListProjection::class,
            self::MANAGER
        );
    }
}