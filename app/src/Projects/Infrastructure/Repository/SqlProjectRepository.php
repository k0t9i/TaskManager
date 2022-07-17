<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\Entity\Project;
use App\Projects\Domain\Repository\ProjectRepositoryInterface;
use App\Projects\Infrastructure\Persistence\Hydrator\Metadata\ProjectStorageMetadata;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\Finder\SqlStorageFinder;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Shared\Infrastructure\Persistence\StorageLoaderInterface;
use App\Shared\Infrastructure\Persistence\StorageSaverInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlProjectRepository implements ProjectRepositoryInterface
{
    use OptimisticLockTrait;

    private readonly StorageMetadataInterface $metadata;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageSaverInterface $storageSaver,
        private readonly StorageLoaderInterface $storageLoader
    ) {
        $this->metadata = new ProjectStorageMetadata();
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
            ->where('id = ?')
            ->setParameters([$id->value]);

        /** @var Project $project */
        [$project, $version] = $this->storageLoader->load(new SqlStorageFinder($builder), $this->metadata);
        if ($project !== null) {
            $this->setVersion($project->getId()->value, $version);
        }
        return $project;
    }

    /**
     * @param Project $project
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(Project $project): void
    {
        $prevVersion = $this->getVersion($project->getId()->value);

        if ($prevVersion > 0) {
            $this->storageSaver->update($project, $this->metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($project, $this->metadata);
        }
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}