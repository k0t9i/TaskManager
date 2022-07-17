<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Tasks\TaskId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\Finder\SqlStorageFinder;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Shared\Infrastructure\Persistence\StorageLoaderInterface;
use App\Shared\Infrastructure\Persistence\StorageSaverInterface;
use App\Tasks\Domain\Entity\TaskManager;
use App\Tasks\Domain\Repository\TaskManagerRepositoryInterface;
use App\Tasks\Infrastructure\Persistence\Hydrator\Metadata\TaskManagerStorageMetadata;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlTaskManagerRepository implements TaskManagerRepositoryInterface
{
    use OptimisticLockTrait;

    private readonly StorageMetadataInterface $metadata;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageSaverInterface $storageSaver,
        private readonly StorageLoaderInterface $storageLoader
    ) {
        $this->metadata = new TaskManagerStorageMetadata();
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
            ->where('project_id = ?')
            ->setParameters([$id->value]);

        return $this->find($builder);
    }

    /**
     * @param TaskId $id
     * @return TaskManager|null
     * @throws Exception
     */
    public function findByTaskId(TaskId $id): ?TaskManager
    {
        $alias = 'rm';
        $builder = $this->queryBuilder()
            ->select($alias . '.*')
            ->leftJoin($alias, 'tasks', 't', 't.task_manager_id = ' . $alias . '.id')
            ->where('t.id = ?')
            ->setParameters([$id->value]);

        return $this->find($builder, $alias);
    }

    private function find(QueryBuilder $builder, ?string $alias = null): ?TaskManager
    {
        /** @var TaskManager $manager */
        [$manager, $version] = $this->storageLoader->load(
            new SqlStorageFinder($builder, $alias),
            $this->metadata
        );
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

        if ($prevVersion > 0) {
            $this->storageSaver->update($manager, $this->metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($manager, $this->metadata);
        }
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}
