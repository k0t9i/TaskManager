<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\Repository\StorageSaverInterface;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Infrastructure\Persistence\Hydrator\Metadata\TaskManagerStorageMetadata;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlTaskManagerRepository implements TaskManagerRepositoryInterface
{
    use OptimisticLockTrait;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TaskManagerDbRetriever $dbRetriever,
        private readonly StorageSaverInterface $storageSaver
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
            $this->setVersion($manager->getId()->value, $version);
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
        $prevVersion = $this->getVersion($manager->getId()->value);

        $metadata = new TaskManagerStorageMetadata();
        if ($prevVersion > 0) {
            $this->storageSaver->update($manager, $metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($manager, $metadata);
        }
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}
