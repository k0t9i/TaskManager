<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Repository\DoctrineCriteriaRepositoryTrait;
use App\Users\Domain\Entity\ProfileProjection;
use App\Users\Domain\Entity\UserProjection;
use App\Users\Domain\Repository\UserQueryRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ObjectRepository;

class DoctrineUserQueryRepository implements UserQueryRepositoryInterface
{
    use DoctrineCriteriaRepositoryTrait;

    private const MANAGER = 'read';

    /**
     * @return UserProjection[]
     * @throws QueryException
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        return $this->findAllByCriteriaInternal($this->getRepository(), $criteria);
    }

    /**
     * @throws QueryException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        return $this->findCountByCriteriaInternal($this->getRepository(), $criteria);
    }

    /**
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function findProfileByCriteria(Criteria $criteria): ?ProfileProjection
    {
        return $this->findByCriteriaInternal($this->getProfileRepository(), $criteria);
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
