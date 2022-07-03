<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Repository;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\DTO\ProjectDTO;
use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Entity\ProjectTask;
use App\Projects\Domain\Factory\ProjectFactory;
use App\Projects\Domain\Factory\ProjectTaskFactory;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Domain\Collection\UserIdCollection;
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
        private readonly ProjectFactory $projectFactory,
        private readonly ProjectTaskFactory $projectTaskFactory,
    ) {
    }

    /**
     * @param ProjectId $id
     * @return Project|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?Project
    {
        $rawProject = $this->queryBuilder()
            ->select('*')
            ->from('projects')
            ->where('id = ?')
            ->setParameters([$id->value])
            ->fetchAssociative();
        if ($rawProject === false) {
            return null;
        }

        $rawParticipants = $this->queryBuilder()
            ->select('user_id')
            ->from('project_participants')
            ->where('project_id = ?')
            ->setParameters([$id->value])
            ->fetchFirstColumn();
        $rawProject['participant_ids'] = new UserIdCollection(
            array_map(fn(string $id) => new UserId($id), $rawParticipants)
        );

        $rawTasks = $this->queryBuilder()
            ->select('*')
            ->from('project_tasks')
            ->where('project_id = ?')
            ->setParameters([$id->value])
            ->fetchAllAssociative();
        $rawProject['tasks'] = new ProjectTaskCollection(
            array_map(function (array $item) {
                return $this->projectTaskFactory->create($item['id'], ProjectTaskDTO::createFromRequest($item));
            }, $rawTasks)
        );

        return $this->projectFactory->create(ProjectDTO::createFromRequest($rawProject));
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
                    $project->getOwner()->userId,
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
                    $project->getOwner()->userId,
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