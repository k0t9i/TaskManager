<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Shared\Application\Service\CriteriaFieldValidatorInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Service\CriteriaToDoctrineCriteriaConverterInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;

trait DoctrineCriteriaRepositoryTrait
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly CriteriaToDoctrineCriteriaConverterInterface $converter,
        private readonly CriteriaFieldValidatorInterface $validator
    ) {
    }

    /**
     * @param EntityRepository $repository
     * @param Criteria $criteria
     * @return array
     * @throws QueryException
     */
    private function findAllByCriteriaInternal(EntityRepository $repository, Criteria $criteria): array
    {
        $this->validator->validate($criteria, $repository->getClassName());

        return $repository->createQueryBuilder('t')
            ->addCriteria($this->converter->convert($criteria))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param EntityRepository $repository
     * @param Criteria $criteria
     * @param string $column
     * @return int
     * @throws QueryException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    private function findCountByCriteriaInternal(EntityRepository $repository, Criteria $criteria): int
    {
        $this->validator->validate($criteria, $repository->getClassName());

        $doctrineCriteria = $this->converter->convert($criteria);
        $doctrineCriteria->setFirstResult(null);
        $doctrineCriteria->setMaxResults(null);
        return $repository->createQueryBuilder('t')
            ->select('count(t)')
            ->addCriteria($doctrineCriteria)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param EntityRepository $repository
     * @param Criteria $criteria
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    private function findByCriteriaInternal(EntityRepository $repository, Criteria $criteria): mixed
    {
        $this->validator->validate($criteria, $repository->getClassName());

        return $repository->createQueryBuilder('t')
            ->addCriteria($this->converter->convert($criteria))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
