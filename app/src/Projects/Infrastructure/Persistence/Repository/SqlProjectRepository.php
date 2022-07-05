<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Repository;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\Factory\TaskStatusFactory;
use App\Shared\Domain\ValueObject\ProjectId;
use App\Shared\Domain\ValueObject\UserId;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlProjectRepository implements ProjectRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProjectDbRetriever $dbRetriever
    ) {
    }

    /**
     * @param UserId $userId
     * @return Project[]
     * @throws Exception
     */
    public function findAllByUserId(UserId $userId): array
    {
        $builder = $this->queryBuilder()
            ->select('p.*')
            ->distinct()
            ->from('projects', 'p')
            ->leftJoin('p', 'project_participants', 'pp', 'p.id = pp.project_id')
            ->where('p.owner_id = ?')
            ->setParameters([$userId->value]);

        return $this->dbRetriever->retrieveAll($builder);
    }

    /**
     * @param ProjectId $id
     * @return Project|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?Project
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->from('projects')
            ->where('id = ?')
            ->setParameters([$id->value]);

        return $this->dbRetriever->retrieveOne($builder);
    }

    /**
     * @param Project $project
     * @throws Exception
     */
    public function save(Project $project): void
    {
        $participants = $project->getParticipants()->getInnerItems();
        /** @var UserId $item */
        foreach ($participants->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('project_participants')
                ->values([
                    'project_id' => '?',
                    'user_id' => '?',
                ])
                ->setParameters([
                    $project->getId()->value,
                    $item->value
                ])
                ->executeStatement();
        }

        $deleted = array_map(fn(UserId $id) => $id->value, $participants->getDeleted());
        $this->queryBuilder()
            ->delete('project_participants')
            ->where('project_id = ?')
            ->andWhere('user_id in (?)')
            ->setParameters([
                $project->getId()->value,
                $deleted
            ], [
                1 => Connection::PARAM_STR_ARRAY
            ])
            ->executeStatement();
        $participants->flush();

        $tasks = $project->getTasks()->getInnerItems();
        /** @var ProjectTask $item */
        foreach ($tasks->getAdded() as $item) {
            $this->queryBuilder()
                ->insert('project_tasks')
                ->values([
                    'id' => '?',
                    'project_id' => '?',
                    'task_id' => '?',
                    'status' => '?',
                    'owner_id' => '?',
                    'start_date' => '?',
                    'finish_date' => '?',
                ])
                ->setParameters([
                    $item->getId()->value,
                    $project->getId()->value,
                    $item->getTaskId()->value,
                    TaskStatusFactory::scalarFromObject($item->getStatus()),
                    $item->getOwnerId()->value,
                    $item->getStartDate()->getValue(),
                    $item->getFinishDate()->getValue()
                ])
                ->executeStatement();
        }
        /** @var ProjectTask $item */
        foreach ($tasks->getUpdated() as $item) {
            $this->queryBuilder()
                ->update('project_tasks')
                ->set('project_id', '?')
                ->set('task_id', '?')
                ->set('status', '?')
                ->set('owner_id', '?')
                ->set('start_date', '?')
                ->set('finish_date', '?')
                ->where('id = ?')
                ->setParameters([
                    $project->getId()->value,
                    $item->getTaskId()->value,
                    TaskStatusFactory::scalarFromObject($item->getStatus()),
                    $item->getOwnerId()->value,
                    $item->getStartDate()->getValue(),
                    $item->getFinishDate()->getValue(),
                    $item->getId()->value
                ])
                ->executeStatement();
        }
        $tasks->flush();


        if (!$this->isExist($project->getId())) {
            $this->queryBuilder()
                ->insert('projects')
                ->values([
                    'id' => '?',
                    'name' => '?',
                    'description' => '?',
                    'finish_date' => '?',
                    'status' => '?',
                    'owner_id' => '?',
                ])
                ->setParameters([
                    $project->getId()->value,
                    $project->getInformation()->name->value,
                    $project->getInformation()->description->value,
                    $project->getInformation()->finishDate->getValue(),
                    ProjectStatusFactory::scalarFromObject($project->getStatus()),
                    $project->getOwner()->userId->value,
                ])
                ->executeStatement();
        } else {
            $this->queryBuilder()
                ->update('projects')
                ->set('name', '?')
                ->set('description', '?')
                ->set('finish_date', '?')
                ->set('status', '?')
                ->set('owner_id', '?')
                ->where('id = ?')
                ->setParameters([
                    $project->getInformation()->name->value,
                    $project->getInformation()->description->value,
                    $project->getInformation()->finishDate->getValue(),
                    ProjectStatusFactory::scalarFromObject($project->getStatus()),
                    $project->getOwner()->userId->value,
                    $project->getId()->value,
                ])
                ->executeStatement();
        }
    }

    /**
     * @param ProjectId $id
     * @return bool
     * @throws Exception
     */
    private function isExist(ProjectId $id): bool
    {
        $count = $this->queryBuilder()
            ->select('count(id)')
            ->from('projects')
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