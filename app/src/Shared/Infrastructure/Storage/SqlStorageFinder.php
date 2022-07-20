<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Storage;

use App\Shared\Application\Storage\StorageFinderInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;

final class SqlStorageFinder implements StorageFinderInterface
{
    public function __construct(
        private readonly ?QueryBuilder $queryBuilder = null
    ) {
    }

    /**
     * @throws Exception
     */
    public function find(string $storageName): array
    {
        $result = $this->queryBuilder
            ->from($storageName)
            ->fetchAssociative();
        if ($result === false) {
            return [];
        }
        return $result;
    }

    /**
     * @throws Exception
     */
    public function findAll(string $storageName): array
    {
        return $this->queryBuilder
            ->from($storageName)
            ->fetchAllAssociative();
    }
}
