<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Repository;

use App\Requests\Domain\Collection\RequestCollection;
use App\Requests\Domain\DTO\RequestDTO;
use App\Requests\Domain\DTO\RequestManagerDTO;
use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Factory\RequestFactory;
use App\Requests\Domain\Factory\RequestManagerFactory;
use App\Requests\Domain\Factory\RequestStatusFactory;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Domain\ValueObject\RequestManagerId;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlRequestManagerRepository implements RequestManagerRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestManagerFactory $requestManagerFactory,
        private readonly RequestFactory $requestFactory,
    ) {
    }

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

    public function save(RequestManager $manager): void
    {
        $participants = $manager->getParticipantIds();
        /** @var UserId $item */
        foreach ($participants->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('request_manager_participants')
                ->values([
                    'request_manager_id' => '?',
                    'user_id' => '?',
                ])
                ->setParameters([
                    $manager->getId()->value,
                    $item->value
                ])
                ->executeStatement();
        }

        $deleted = array_map(fn(UserId $id) => $id->value, $participants->getDeleted());
        $this->queryBuilder()
            ->delete('request_manager_participants')
            ->where('request_manager_id = ?')
            ->andWhere('user_id in (?)')
            ->setParameters([
                $manager->getId()->value,
                $deleted
            ], [
                1 => Connection::PARAM_STR_ARRAY
            ])
            ->executeStatement();
        $participants->flush();

        $requests = $manager->getRequests();
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
                    $manager->getId()->value,
                    $item->getUserId()->value,
                    RequestStatusFactory::scalarFromObject($item->getStatus()),
                    $item->getChangeDate()->getValue()
                ])
                ->executeStatement();
        }
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
                    $manager->getId()->value,
                    $item->getUserId()->value,
                    RequestStatusFactory::scalarFromObject($item->getStatus()),
                    $item->getChangeDate()->getValue(),
                    $item->getId()->value,
                ])
                ->executeStatement();
        }
        $requests->flush();


        if (!$this->isExist($manager->getId())) {
            $this->queryBuilder()
                ->insert('request_managers')
                ->values([
                    'id' => '?',
                    'project_id' => '?',
                    'status' => '?',
                    'owner_id' => '?',
                ])
                ->setParameters([
                    $manager->getId()->value,
                    $manager->getProjectId()->value,
                    ProjectStatusFactory::scalarFromObject($manager->getStatus()),
                    $manager->getOwnerId()->value,
                ])
                ->executeStatement();
        } else {
            $this->queryBuilder()
                ->update('request_managers')
                ->set('project_id', '?')
                ->set('status', '?')
                ->set('owner_id', '?')
                ->where('id = ?')
                ->setParameters([
                    $manager->getProjectId()->value,
                    ProjectStatusFactory::scalarFromObject($manager->getStatus()),
                    $manager->getOwnerId()->value,
                    $manager->getId()->value,
                ])
                ->executeStatement();
        }
    }

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
                return $this->requestFactory->create(RequestDTO::createFromRequest($item));
            }, $rawRequests)
        );

        return $this->requestManagerFactory->create(RequestManagerDTO::createFromRequest($rawManager));
    }

    private function isExist(RequestManagerId $id): bool
    {
        $count = $this->queryBuilder()
            ->select('count(id)')
            ->from('request_managers')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchOne();
        return $count > 0;
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}
