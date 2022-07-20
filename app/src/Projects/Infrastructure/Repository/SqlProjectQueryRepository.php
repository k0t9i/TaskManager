<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Repository;

use App\Projects\Domain\DTO\ProjectListResponseDTO;
use App\Projects\Domain\DTO\ProjectResponseDTO;
use App\Projects\Domain\Repository\ProjectQueryRepositoryInterface;
use App\Projects\Infrastructure\Persistence\Hydrator\Metadata\ProjectListResponseStorageMetadata;
use App\Projects\Infrastructure\Persistence\Hydrator\Metadata\ProjectResponseStorageMetadata;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Finder\SqlStorageFinder;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Persistence\StorageLoaderInterface;
use App\Shared\Infrastructure\Service\CriteriaToQueryBuilderConverter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class SqlProjectQueryRepository implements ProjectQueryRepositoryInterface
{
    private const CONNECTION = 'read';

    private readonly StorageMetadataInterface $listMetadata;
    private readonly StorageMetadataInterface $metadata;

    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly StorageLoaderInterface $storageLoader,
        private readonly CriteriaToQueryBuilderConverter $converter
    ) {
        $this->listMetadata = new ProjectListResponseStorageMetadata();
        $this->metadata = new ProjectResponseStorageMetadata();
    }

    /**
     * @param Criteria $criteria
     * @return ProjectListResponseDTO[]
     * @throws Exception
     */
    public function findAllByCriteria(Criteria $criteria): array
    {
        $builder = $this->queryBuilder()
            ->select('*');
        $this->converter->convert($builder, $criteria);

        $rawItems = $this->storageLoader->loadAll(new SqlStorageFinder($builder), $this->listMetadata);
        $result = [];
        foreach ($rawItems as [$item, $version]) {
            $result[] = $item;
        }

        return $result;
    }

    /**
     * @param Criteria $criteria
     * @return int
     * @throws Exception
     */
    public function findCountByCriteria(Criteria $criteria): int
    {
        $builder = $this->queryBuilder()
            ->select('count(*)')
            ->from($this->listMetadata->getStorageName());

        $this->converter->convert($builder, $criteria);

        $builder->setFirstResult(0);
        $builder->setMaxResults(null);

        return $builder->fetchOne();
    }

    /**
     * @param ProjectId $id
     * @param UserId $userId
     * @return ProjectResponseDTO|null
     * @throws Exception
     */
    public function findByIdAndUserId(ProjectId $id, UserId $userId): ?ProjectResponseDTO
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->where('id = ?')
            ->andWhere('user_id = ?')
            ->setParameters([
                $id->value,
                $userId->value
            ]);

        return $this->storageLoader->load(new SqlStorageFinder($builder), $this->metadata)[0];
    }

    /**
     * @param ProjectId $id
     * @return ProjectResponseDTO|null
     * @throws Exception
     */
    public function findById(ProjectId $id): ?ProjectResponseDTO
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->where('id = ?')
            ->setParameters([
                $id->value
            ]);

        return $this->storageLoader->load(new SqlStorageFinder($builder), $this->metadata)[0];
    }

    private function queryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->managerRegistry->getConnection(self::CONNECTION);
        return $connection->createQueryBuilder();
    }
}