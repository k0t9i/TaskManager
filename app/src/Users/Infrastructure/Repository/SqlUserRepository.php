<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Repository;

use App\Shared\Domain\ValueObject\Users\UserEmail;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Exception\OptimisticLockException;
use App\Shared\Infrastructure\Persistence\Finder\SqlStorageFinder;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Infrastructure\Persistence\OptimisticLockTrait;
use App\Shared\Infrastructure\Persistence\StorageLoaderInterface;
use App\Shared\Infrastructure\Persistence\StorageSaverInterface;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use App\Users\Infrastructure\Persistence\Hydrator\Metadata\UserStorageMetadata;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;

class SqlUserRepository implements UserRepositoryInterface
{
    use OptimisticLockTrait;

    private readonly StorageMetadataInterface $metadata;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StorageSaverInterface $storageSaver,
        private readonly StorageLoaderInterface $storageLoader
    ) {
        $this->metadata = new UserStorageMetadata();
    }

    /**
     * @param UserId $id
     * @return User|null
     * @throws Exception
     */
    public function findById(UserId $id): ?User
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->where('id = ?')
            ->setParameters([$id->value]);

        return $this->find($builder);
    }

    /**
     * @param UserEmail $email
     * @return User|null
     * @throws Exception
     */
    public function findByEmail(UserEmail $email): ?User
    {
        $builder = $this->queryBuilder()
            ->select('*')
            ->where('email = ?')
            ->setParameters([$email->value]);

        return $this->find($builder);
    }

    /**
     * @param User $user
     * @throws Exception
     * @throws OptimisticLockException
     */
    public function save(User $user): void
    {
        $prevVersion = $this->getVersion($user->getId()->value);

        if ($prevVersion > 0) {
            $this->storageSaver->update($user, $this->metadata, $prevVersion);
        } else {
            $this->storageSaver->insert($user, $this->metadata);
        }
    }

    private function find(QueryBuilder $builder): ?User
    {
        /** @var User $user */
        [$user, $version] = $this->storageLoader->load(
            new SqlStorageFinder($builder),
            $this->metadata
        );
        if ($user !== null) {
            $this->setVersion($user->getId()->value, $version);
        }
        return $user;
    }

    private function queryBuilder(): QueryBuilder
    {
        return $this->entityManager->getConnection()->createQueryBuilder();
    }
}