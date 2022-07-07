<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Tasks\Domain\Collection\TaskCollection;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Domain\ValueObject\TaskLink;
use App\Tasks\Domain\ValueObject\TaskManagerId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlTaskManagerRepository implements TaskManagerRepositoryInterface
{
    use OptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskManagerDbRetriever $dbRetriever,
    ) {
    }

    /**
     * @param ProjectId $id
     * @return TaskManager|null
     * @throws Exception
     */
    public function findByProjectId(ProjectId $id): ?TaskManager
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->from('task_managers')
            ->where('project_id = ?')
            ->setParameters([$id->value]);

        return $this->retrieveOneAndSaveVersion($builder);
    }

    /**
     * @param TaskId $id
     * @return TaskManager|null
     * @throws Exception
     */
    public function findByTaskId(TaskId $id): ?TaskManager
    {
        $builder = $this->queryBuilder()
            ->select('tm.*')
            ->from('task_managers', 'tm')
            ->leftJoin('tm', 'tasks', 't', 't.task_manager_id = tm.id')
            ->where('t.id = ?')
            ->setParameters([$id->value]);

        return $this->retrieveOneAndSaveVersion($builder);
    }

    /**
     * @param $builder
     * @return TaskManager|null
     * @throws Exception
     */
    private function retrieveOneAndSaveVersion($builder): ?TaskManager
    {
        /** @var TaskManager $manager */
        [$manager, $version] = $this->dbRetriever->retrieveOne($builder);
        if ($manager !== null) {
            $this->saveVersion($manager->getId()->value, $version);
        }
        return $manager;
    }

    /**
     * @param TaskManager $manager
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(TaskManager $manager): void
    {
        $version = $this->getVersion($manager->getId());
        $isExist = $version > 0;
        $this->ensureIsVersionLesserThanPrevious($manager->getId()->value, $version);
        $version += 1;

        $participants = $manager->getParticipants()->getInnerItems();
        $this->insertParticipants($participants, $manager->getId()->value);
        $this->deleteParticipants($participants, $manager->getId()->value);
        $participants->flush();

        $tasks = $manager->getTasks();
        $this->insertTasks($tasks, $manager->getId()->value);
        $this->updateTasks($tasks, $manager->getId()->value);
        /** @var Task $task */
        foreach ($tasks as $task) {
            $this->insertLinks($task);
            $this->deleteLinks($task);
            $task->getLinks()->flush();
        }
        $tasks->flush();

        if ($isExist) {
            $this->updateManager($manager, $version);
        } else {
            $this->insertManager($manager, $version);
        }
    }

    /**
     * @param TaskManagerId $id
     * @return int
     * @throws Exception
     */
    private function getVersion(TaskManagerId $id): int
    {
        $version = $this->queryBuilder()
            ->select('version')
            ->from('task_managers')
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
                ->insert('task_manager_participants')
                ->values([
                    'task_manager_id' => '?',
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
     * @return array
     * @throws Exception
     */
    private function deleteParticipants(UserIdCollection $participants, string $managerId): array
    {
        $deleted = array_map(fn(UserId $id) => $id->value, $participants->getDeleted());
        $this->queryBuilder()
            ->delete('task_manager_participants')
            ->where('task_manager_id = ?')
            ->andWhere('user_id in (?)')
            ->setParameters([
                $managerId,
                $deleted
            ], [
                1 => Connection::PARAM_STR_ARRAY
            ])
            ->executeStatement();
        return $deleted;
    }

    /**
     * @param TaskCollection $tasks
     * @param string $managerId
     * @throws Exception
     */
    private function insertTasks(TaskCollection $tasks, string $managerId): void
    {
        /** @var Task $item */
        foreach ($tasks->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('tasks')
                ->values([
                    'id' => '?',
                    'task_manager_id' => '?',
                    'name' => '?',
                    'brief' => '?',
                    'description' => '?',
                    'start_date' => '?',
                    'finish_date' => '?',
                    'owner_id' => '?',
                    'status' => '?',
                ])
                ->setParameters([
                    $item->getId()->value,
                    $managerId,
                    $item->getInformation()->name->value,
                    $item->getInformation()->brief->value,
                    $item->getInformation()->description->value,
                    $item->getInformation()->startDate->getValue(),
                    $item->getInformation()->finishDate->getValue(),
                    $item->getOwnerId()->value,
                    TaskStatusFactory::scalarFromObject($item->getStatus())
                ])
                ->executeStatement();
        }
    }

    /**
     * @param TaskCollection $tasks
     * @param string $managerId
     * @throws Exception
     */
    private function updateTasks(TaskCollection $tasks, string $managerId): void
    {
        /** @var Task $item */
        foreach ($tasks->getUpdated() as $item) {
            $this->queryBuilder()
                ->update('tasks')
                ->set('task_manager_id', '?')
                ->set('name', '?')
                ->set('brief', '?')
                ->set('description', '?')
                ->set('start_date', '?')
                ->set('finish_date', '?')
                ->set('owner_id', '?')
                ->set('status', '?')
                ->where('id = ?')
                ->setParameters([
                    $managerId,
                    $item->getInformation()->name->value,
                    $item->getInformation()->brief->value,
                    $item->getInformation()->description->value,
                    $item->getInformation()->startDate->getValue(),
                    $item->getInformation()->finishDate->getValue(),
                    $item->getOwnerId()->value,
                    TaskStatusFactory::scalarFromObject($item->getStatus()),
                    $item->getId()->value,
                ])
                ->executeStatement();
        }
    }

    /**
     * @param Task $task
     * @throws Exception
     */
    private function insertLinks(Task $task): void
    {
        /** @var TaskLink $item */
        foreach ($task->getLinks()->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('task_links')
                ->values([
                    'from_task_id' => '?',
                    'to_task_id' => '?',
                ])
                ->setParameters([
                    $task->getId()->value,
                    $item->toTaskId->value
                ])
                ->executeStatement();
        }
    }

    /**
     * @param Task $task
     * @throws Exception
     */
    private function deleteLinks(Task $task): void
    {
        $deleted = array_map(fn(TaskLink $link) => $link->toTaskId->value, $task->getLinks()->getDeleted());
        $this->queryBuilder()
            ->delete('task_links')
            ->where('from_task_id = ?')
            ->andWhere('to_task_id in (?)')
            ->setParameters([
                $task->getId()->value,
                $deleted
            ], [
                1 => Connection::PARAM_STR_ARRAY
            ])
            ->executeStatement();
    }

    /**
     * @param TaskManager $manager
     * @param int $version
     * @throws Exception
     */
    private function updateManager(TaskManager $manager, int $version): void
    {
        $this->queryBuilder()
            ->update('task_managers')
            ->set('project_id', '?')
            ->set('status', '?')
            ->set('owner_id', '?')
            ->set('finish_date', '?')
            ->set('version', '?')
            ->where('id = ?')
            ->setParameters([
                $manager->getProjectId()->value,
                ProjectStatusFactory::scalarFromObject($manager->getStatus()),
                $manager->getOwner()->userId->value,
                $manager->getFinishDate()->getValue(),
                $version,
                $manager->getId()->value,
            ])
            ->executeStatement();
    }

    /**
     * @param TaskManager $manager
     * @param int $version
     * @throws Exception
     */
    private function insertManager(TaskManager $manager, int $version): void
    {
        $this->queryBuilder()
            ->insert('task_managers')
            ->values([
                'id' => '?',
                'project_id' => '?',
                'status' => '?',
                'owner_id' => '?',
                'finish_date' => '?',
                'version' => '?',
            ])
            ->setParameters([
                $manager->getId()->value,
                $manager->getProjectId()->value,
                ProjectStatusFactory::scalarFromObject($manager->getStatus()),
                $manager->getOwner()->userId->value,
                $manager->getFinishDate()->getValue(),
                $version
            ])
            ->executeStatement();
    }
}
