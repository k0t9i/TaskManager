<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Entity\RequestListProjection;
use App\Requests\Domain\Repository\RequestQueryRepositoryInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Repository\SqlCriteriaRepositoryTrait;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\QueryException;
use Doctrine\Persistence\ObjectRepository;
use ReflectionException;

class SqlRequestQueryRepository implements RequestQueryRepositoryInterface
{
    use SqlCriteriaRepositoryTrait;

    private const MANAGER = 'read';

    /**
     * @param Criteria $criteria
     * @return array
     * @throws QueryException
     * @throws ReflectionException
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        return $this->findAllByCriteriaInternal($this->getRepository(), $criteria);
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
        return $this->findCountByCriteriaInternal($this->getRepository(), $criteria);
    }

    private function getRepository(): ObjectRepository|EntityRepository
    {
        return $this->managerRegistry->getRepository(
            RequestListProjection::class,
            self::MANAGER
        );
    }
}