<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Shared\Domain\Entity\SharedUser;
use App\Shared\Domain\Repository\SharedUserRepositoryInterface;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Finder\SqlStorageFinder;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\SharedUserStorageMetadata;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Persistence\StorageLoaderInterface;
use App\Shared\Infrastructure\Persistence\StorageSaverInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

final class SqlSharedUserRepository implements SharedUserRepositoryInterface
{
    private readonly StorageMetadataInterface $metadata;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageSaverInterface $storageSaver,
        private readonly StorageLoaderInterface $storageLoader
    ) {
        $this->metadata = new SharedUserStorageMetadata();
    }

    /**
     * @param UserId $id
     * @return SharedUser|null
     * @throws Exception
     */
    public function findById(UserId $id): ?SharedUser
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->where('id = ?')
            ->setParameters([$id->value]);

        /** @var SharedUser $user */
        [$user] = $this->storageLoader->load(new SqlStorageFinder($builder), $this->metadata);
        return $user;
    }

    /**
     * @param SharedUser $user
     * @throws Exception
     */
    public function save(SharedUser $user): void
    {
        if ($this->isExist($user->getId())) {
            $this->storageSaver->update($user, $this->metadata);
        } else {
            $this->storageSaver->insert($user, $this->metadata, false);
        }
    }

    /**
     * @param UserId $id
     * @return bool
     * @throws Exception
     */
    private function isExist(UserId $id): bool
    {
        $count = $this->queryBuilder()
            ->select('count(id)')
            ->from($this->metadata->getStorageName())
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