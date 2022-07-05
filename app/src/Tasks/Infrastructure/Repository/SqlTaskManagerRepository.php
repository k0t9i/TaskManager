<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\TaskId;
use App\Shared\Domain\ValueObject\UserId;
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

        return $this->dbRetriever->retrieveOne($builder);
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

        return $this->dbRetriever->retrieveOne($builder);
    }

    /**
     * @param TaskManager $manager
     * @throws Exception
     */
    public function save(TaskManager $manager): void
    {
        $participants = $manager->getParticipantIds();
        /** @var UserId $item */
        foreach ($participants->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('task_manager_participants')
                ->values([
                    'task_manager_id' => '?',
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
            ->delete('task_manager_participants')
            ->where('task_manager_id = ?')
            ->andWhere('user_id in (?)')
            ->setParameters([
                $manager->getId()->value,
                $deleted
            ], [
                1 => Connection::PARAM_STR_ARRAY
            ])
            ->executeStatement();
        $participants->flush();

        $tasks = $manager->getTasks();
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
                    $manager->getId()->value,
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
                    $manager->getId()->value,
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
        /** @var Task $task */
        foreach ($tasks as $task) {
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
            $task->getLinks()->flush();
        }
        $tasks->flush();

        if (!$this->isExist($manager->getId())) {
            $this->queryBuilder()
                ->insert('task_managers')
                ->values([
                    'id' => '?',
                    'project_id' => '?',
                    'status' => '?',
                    'owner_id' => '?',
                    'finish_date' => '?',
                ])
                ->setParameters([
                    $manager->getId()->value,
                    $manager->getProjectId()->value,
                    ProjectStatusFactory::scalarFromObject($manager->getStatus()),
                    $manager->getOwnerId()->value,
                    $manager->getFinishDate()->getValue(),
                ])
                ->executeStatement();
        } else {
            $this->queryBuilder()
                ->update('task_managers')
                ->set('project_id', '?')
                ->set('status', '?')
                ->set('owner_id', '?')
                ->set('finish_date', '?')
                ->where('id = ?')
                ->setParameters([
                    $manager->getProjectId()->value,
                    ProjectStatusFactory::scalarFromObject($manager->getStatus()),
                    $manager->getOwnerId()->value,
                    $manager->getFinishDate()->getValue(),
                    $manager->getId()->value,
                ])
                ->executeStatement();
        }
    }

    /**
     * @param TaskManagerId $id
     * @return bool
     * @throws Exception
     */
    private function isExist(TaskManagerId $id): bool
    {
        $count = $this->queryBuilder()
            ->select('count(id)')
            ->from('task_managers')
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
