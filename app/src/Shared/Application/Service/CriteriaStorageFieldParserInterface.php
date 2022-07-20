<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\CriteriaJoinDTO;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;

interface CriteriaStorageFieldParserInterface
{
    /**
     * @return CriteriaJoinDTO[]
     */
    public function parseJoins(Criteria $criteria, StorageMetadataInterface $metadata): array;
    public function parseColumns(array $joins, Criteria $criteria, StorageMetadataInterface $metadata): array;
}