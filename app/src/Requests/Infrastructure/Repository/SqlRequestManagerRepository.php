<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\DTO\RequestDTO;
use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestFactory;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Infrastructure\Persistence\Hydrator\Metadata\RequestManagerStorageMetadata;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Repository\StorageSaverInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlRequestManagerRepository implements RequestManagerRepositoryInterface
{
    use OptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestManagerFactory $requestManagerFactory,
        private readonly RequestFactory $requestFactory,
        private readonly StorageSaverInterface $storageSaver
    ) {
    }

    /**
     * @param ProjectId $id
     * @return RequestManager|null
     * @throws Exception
     */
    public function findByProjectId(ProjectId $id): ?RequestManager
    {
        $rawManager = $this->queryBuilder()
            ->select('*')
            ->from('request_managers')
            ->where('project_id = ?')
            ->setParameters([$id->value])
            ->fetchAssociative();
        if ($rawManager === false) {
            return null;
        }

        return $this->find($rawManager);
    }

    /**
     * @param RequestId $id
     * @return RequestManager|null
     * @throws Exception
     */
    public function findByRequestId(RequestId $id): ?RequestManager
    {
        $rawManager = $this->queryBuilder()
            ->select('rm.*')
            ->from('request_managers', 'rm')
            ->leftJoin('rm', 'requests', 'r', 'r.request_manager_id = rm.id')
            ->where('r.id = ?')
            ->setParameters([$id->value])
            ->fetchAssociative();
        if ($rawManager === false) {
            return null;
        }

        return $this->find($rawManager);
    }

    /**
     * @param RequestManager $manager
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(RequestManager $manager): void
    {
        $prevVersion = $this->getVersion($manager->getId()->value);

        $metadata = new RequestManagerStorageMetadata();
        if ($prevVersion > 0) {
            $this->storageSaver->update($manager, $metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($manager, $metadata);
        }
    }

    /**
     * @param array $rawManager
     * @return RequestManager|null
     * @throws Exception
     */
    private function find(array $rawManager): ?RequestManager
    {
        $rawParticipants = $this->queryBuilder()
            ->select('user_id')
            ->from('request_manager_participants')
            ->where('request_manager_id = ?')
            ->setParameters([$rawManager['id']])
            ->fetchFirstColumn();
        $rawManager['participant_ids'] = new UserIdCollection(
            array_map(fn(string $id) => new UserId($id), $rawParticipants)
        );

        $rawRequests = $this->queryBuilder()
            ->select('*')
            ->from('requests')
            ->where('request_manager_id = ?')
            ->setParameters([$rawManager['id']])
            ->fetchAllAssociative();
        $rawManager['requests'] = new RequestCollection(
            array_map(function (array $item) {
                return $this->requestFactory->create(RequestDTO::create($item));
            }, $rawRequests)
        );

        $this->setVersion($rawManager['id'], $rawManager['version']);

        return $this->requestManagerFactory->create(RequestManagerDTO::create($rawManager));
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}
