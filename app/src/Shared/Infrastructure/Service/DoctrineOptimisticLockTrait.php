<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Infrastructure\Exception\OptimisticLockException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException as DoctrineOptimisticLockException;
use Doctrine\ORM\UnitOfWork;

trait DoctrineOptimisticLockTrait
{
    /**
     * @param object $proxy
     * @param int $version
     * @throws OptimisticLockException
     */
    private function lock(EntityManagerInterface $entityManager, DoctrineVersionedProxyInterface $proxy): void
    {
        $uow = $entityManager->getUnitOfWork();
        if ($uow->getEntityState($proxy) === UnitOfWork::STATE_MANAGED) {
            $version = $proxy->getVersion();
            $entityManager->refresh($proxy);
            try {
                $entityManager->lock($proxy, LockMode::OPTIMISTIC, $version);
            } catch (DoctrineOptimisticLockException) {
                throw new OptimisticLockException($proxy->getVersion(), $version);
            }
        }
    }
}
