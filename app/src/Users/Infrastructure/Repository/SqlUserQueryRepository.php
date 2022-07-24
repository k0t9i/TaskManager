<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Application\Service\CriteriaFieldValidatorInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Service\CriteriaToDoctrineCriteriaConverterInterface;
use App\Users\Domain\Entity\ProfileProjection;
use App\Users\Domain\Entity\UserProjection;
use App\Users\Domain\Repository\UserQueryRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;

class SqlUserQueryRepository implements UserQueryRepositoryInterface
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
     * @return UserProjection[]
     * @throws QueryException
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        $this->validator->validate($criteria, UserProjection::class);

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
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        $this->validator->validate($criteria, UserProjection::class);

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
     * @return ProfileProjection|null
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function findProfileByCriteria(Criteria $criteria): ?ProfileProjection
    {
        $this->validator->validate($criteria, ProfileProjection::class);

        return $this->getProfileRepository()
            ->createQueryBuilder('t')
            ->addCriteria($this->converter->convert($criteria))
            ->getQuery()
            ->getOneOrNullResult();
    }

    private function getRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            UserProjection::class,
            self::MANAGER
        );
    }

    private function getProfileRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            ProfileProjection::class,
            self::MANAGER
        );
    }
}