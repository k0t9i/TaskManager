<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use App\Tasks\Domain\Entity\TaskListProjection;
use App\Tasks\Domain\Entity\TaskProjection;
use App\Tasks\Domain\Repository\TaskQueryRepositoryInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ObjectRepository;

class SqlTaskQueryRepository implements TaskQueryRepositoryInterface
{
    use SqlCriteriaRepositoryTrait;

    private const MANAGER = 'read';

    /**
     * @param Criteria $criteria
     * @return TaskListProjection[]
     * @throws QueryException
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        return $this->findAllByCriteriaInternal($this->getListRepository(), $criteria);
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
        return $this->findCountByCriteriaInternal($this->getListRepository(), $criteria);
    }

    /**
     * @param Criteria $criteria
     * @return TaskProjection|null
     * @throws NonUniqueResultException
     * @throws QueryException
     */
    public function findByCriteria(Criteria $criteria): ?TaskProjection
    {
        return $this->findByCriteriaInternal($this->getRepository(), $criteria);
    }

    private function getListRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            TaskListProjection::class,
            self::MANAGER
        );
    }

    private function getRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            TaskProjection::class,
            self::MANAGER
        );
    }
}