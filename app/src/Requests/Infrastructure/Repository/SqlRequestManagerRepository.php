<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Repository;

use App\Requests\Domain\Entity\RequestManager;
use App\Requests\Domain\Repository\RequestManagerRepositoryInterface;
use App\Requests\Domain\ValueObject\RequestId;
use App\Requests\Infrastructure\Persistence\Hydrator\Metadata\RequestManagerStorageMetadata;
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

class SqlRequestManagerRepository implements RequestManagerRepositoryInterface
{
    use OptimisticLockTrait;

    private readonly StorageMetadataInterface $metadata;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageSaverInterface $storageSaver,
        private readonly StorageLoaderInterface $storageLoader
    ) {
        $this->metadata = new RequestManagerStorageMetadata();
    }

    /**
     * @param ProjectId $id
     * @return RequestManager|null
     * @throws Exception
     */
    public function findByProjectId(ProjectId $id): ?RequestManager
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->where('project_id = ?')
            ->setParameters([$id->value]);

        return $this->find($builder);
    }

    /**
     * @param RequestId $id
     * @return RequestManager|null
     * @throws Exception
     */
    public function findByRequestId(RequestId $id): ?RequestManager
    {
        $alias = 'rm';
        $builder = $this->queryBuilder()
            ->select($alias . '.*')
            ->leftJoin($alias, 'requests', 'r', 'r.request_manager_id = ' . $alias . '.id')
            ->where('r.id = ?')
            ->setParameters([$id->value]);

        return $this->find($builder, 'rm');
    }

    /**
     * @param RequestManager $manager
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(RequestManager $manager): void
    {
        $prevVersion = $this->getVersion($manager->getId()->value);

        if ($prevVersion > 0) {
            $this->storageSaver->update($manager, $this->metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($manager, $this->metadata);
        }
    }

    private function find(QueryBuilder $builder, ?string $alias = null): ?RequestManager
    {
        /** @var RequestManager $manager */
        [$manager, $version] = $this->storageLoader->load(
            new SqlStorageFinder($builder, $alias),
            $this->metadata
        );
        if ($manager !== null) {
            $this->setVersion($manager->getId()->value, $version);
        }
        return $manager;
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}
