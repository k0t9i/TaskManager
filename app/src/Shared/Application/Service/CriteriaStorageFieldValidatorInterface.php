<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;

interface CriteriaStorageFieldValidatorInterface
{
    public function validate(Criteria $criteria, StorageMetadataInterface $metadata): void;
}
