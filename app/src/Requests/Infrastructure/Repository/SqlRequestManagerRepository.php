<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\DTO\RequestDTO;
use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestFactory;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use Doctrine\DBAL\Connection;
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
        $version = $this->getVersion($manager->getId());
        $isExist = $version > 0;
        $this->ensureIsVersionLesserThanPrevious($manager->getId()->value, $version);
        $version += 1;

        $participants = $manager->getParticipants()->getInnerItems();
        $this->insertParticipants($participants, $manager->getId()->value);
        $this->deleteParticipants($participants, $manager->getId()->value);
        $participants->flush();

        $requests = $manager->getRequests()->getInnerItems();
        $this->insertRequests($requests, $manager->getId()->value);
        $this->updateRequests($requests, $manager->getId()->value);
        $requests->flush();


        if ($isExist) {
            $this->updateManager($manager, $version);
        } else {
            $this->insertManager($manager, $version);
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

        $this->saveVersion($rawManager['id'], $rawManager['version']);

        return $this->requestManagerFactory->create(RequestManagerDTO::create($rawManager));
    }

    /**
     * @param RequestManagerId $id
     * @return int
     * @throws Exception
     */
    private function getVersion(RequestManagerId $id): int
    {
        $version = $this->queryBuilder()
            ->select('version')
            ->from('request_managers')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchOne();
        return $version ?: 0;
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }

    /**
     * @param UserIdCollection $participants
     * @param string $managerId
     * @throws Exception
     */
    private function insertParticipants(UserIdCollection $participants, string $managerId): void
    {
        /** @var UserId $item */
        foreach ($participants->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('request_manager_participants')
                ->values([
                    'request_manager_id' => '?',
                    'user_id' => '?',
                ])
                ->setParameters([
                    $managerId,
                    $item->value
                ])
                ->executeStatement();
        }
    }

    /**
     * @param UserIdCollection $participants
     * @param string $managerId
     * @throws Exception
     */
    private function deleteParticipants(UserIdCollection $participants, string $managerId): void
    {
        $deleted = array_map(fn(UserId $id) => $id->value, $participants->getDeleted());
        $this->queryBuilder()
            ->delete('request_manager_participants')
            ->where('request_manager_id = ?')
            ->andWhere('user_id in (?)')
            ->setParameters([
                $managerId,
                $deleted
            ], [
                1 => Connection::PARAM_STR_ARRAY
            ])
            ->executeStatement();
    }

    /**
     * @param RequestCollection $requests
     * @param string $managerId
     * @throws Exception
     */
    private function insertRequests(RequestCollection $requests, string $managerId): void
    {
        /** @var Request $item */
        foreach ($requests->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('requests')
                ->values([
                    'id' => '?',
                    'request_manager_id' => '?',
                    'user_id' => '?',
                    'status' => '?',
                    'change_date' => '?'
                ])
                ->setParameters([
                    $item->getId()->value,
                    $managerId,
                    $item->getUserId()->value,
                    $item->getStatus()->getScalar(),
                    $item->getChangeDate()->getValue()
                ])
                ->executeStatement();
        }
    }

    /**
     * @param RequestCollection $requests
     * @param string $managerId
     * @throws Exception
     */
    private function updateRequests(RequestCollection $requests, string $managerId): void
    {
        /** @var Request $item */
        foreach ($requests->getUpdated() as $item) {
            $this->queryBuilder()
                ->update('requests')
                ->set('request_manager_id', '?')
                ->set('user_id', '?')
                ->set('status', '?')
                ->set('change_date', '?')
                ->where('id = ?')
                ->setParameters([
                    $managerId,
                    $item->getUserId()->value,
                    $item->getStatus()->getScalar(),
                    $item->getChangeDate()->getValue(),
                    $item->getId()->value,
                ])
                ->executeStatement();
        }
    }

    /**
     * @param RequestManager $manager
     * @param int $version
     * @throws Exception
     */
    private function updateManager(RequestManager $manager, int $version): void
    {
        $this->queryBuilder()
            ->update('request_managers')
            ->set('project_id', '?')
            ->set('status', '?')
            ->set('owner_id', '?')
            ->set('version', '?')
            ->where('id = ?')
            ->setParameters([
                $manager->getProjectId()->value,
                $manager->getStatus()->getScalar(),
                $manager->getOwner()->userId->value,
                $version,
                $manager->getId()->value,
            ])
            ->executeStatement();
    }

    /**
     * @param RequestManager $manager
     * @param int $version
     * @throws Exception
     */
    private function insertManager(RequestManager $manager, int $version): void
    {
        $this->queryBuilder()
            ->insert('request_managers')
            ->values([
                'id' => '?',
                'project_id' => '?',
                'status' => '?',
                'owner_id' => '?',
                'version' => '?',
            ])
            ->setParameters([
                $manager->getId()->value,
                $manager->getProjectId()->value,
                $manager->getStatus()->getScalar(),
                $manager->getOwner()->userId->value,
                $version
            ])
            ->executeStatement();
    }
}
