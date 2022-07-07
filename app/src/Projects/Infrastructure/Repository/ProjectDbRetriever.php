<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Collection\ProjectTaskCollection;
use App\Projects\Domain\DTO\ProjectDTO;
use App\Projects\Domain\DTO\ProjectTaskDTO;
use App\Projects\Domain\Factory\ProjectFactory;
use App\Projects\Domain\Factory\ProjectTaskFactory;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\UserId;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final class ProjectDbRetriever
{
    public function __construct(
        private readonly ProjectFactory $projectFactory,
        private readonly ProjectTaskFactory $projectTaskFactory
    ) {
    }

    /**
     * @param QueryBuilder $builder
     * @return array
     * @throws Exception
     */
    public function retrieveAll(QueryBuilder $builder): array
    {
        $rawProjects = $builder->fetchAllAssociative();

        $result = [];
        foreach ($rawProjects as $rawProject) {
            $result[] = $this->retrieve($builder, $rawProject);
        }
        return $result;
    }

    /**
     * @param QueryBuilder $builder
     * @return array
     * @throws Exception
     */
    public function retrieveOne(QueryBuilder $builder): array
    {
        $rawProject = $builder->fetchAssociative();
        if ($rawProject === false) {
            return [null, 0];
        }

        return $this->retrieve($builder, $rawProject);
    }

    /**
     * @param QueryBuilder $builder
     * @param array $rawProject
     * @return array
     * @throws Exception
     */
    private function retrieve(QueryBuilder $builder, array $rawProject): array
    {
        $rawProject['participant_ids'] = $this->retrieveParticipants($builder, $rawProject['id']);
        $rawProject['tasks'] = $this->retrieveTasks($builder, $rawProject['id']);

        return [$this->projectFactory->create(ProjectDTO::create($rawProject)), $rawProject['version']];
    }

    /**
     * @param QueryBuilder $builder
     * @param string $projectId
     * @return UserIdCollection
     * @throws Exception
     */
    private function retrieveParticipants(QueryBuilder $builder, string $projectId): UserIdCollection
    {
        $this->resetBuilder($builder);

        $rawParticipants = $builder
            ->select('user_id')
            ->from('project_participants')
            ->where('project_id = ?')
            ->setParameters([$projectId])
            ->fetchFirstColumn();
        return new UserIdCollection(
            array_map(fn(string $id) => new UserId($id), $rawParticipants)
        );
    }

    /**
     * @param QueryBuilder $builder
     * @param string $projectId
     * @return ProjectTaskCollection
     * @throws Exception
     */
    private function retrieveTasks(QueryBuilder $builder, string $projectId): ProjectTaskCollection
    {
        $this->resetBuilder($builder);

        $rawTasks = $builder
            ->select('*')
            ->from('project_tasks')
            ->where('project_id = ?')
            ->setParameters([$projectId])
            ->fetchAllAssociative();
        return new ProjectTaskCollection(
            array_map(function (array $item) {
                return $this->projectTaskFactory->create($item['id'], ProjectTaskDTO::create($item));
            }, $rawTasks)
        );
    }

    private function resetBuilder(QueryBuilder $criteria): void
    {
        $criteria->resetQueryParts();
    }
}
