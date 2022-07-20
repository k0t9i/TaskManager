<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\DTO;

use App\Shared\Domain\Collection\CollectionInterface;

final class RehydratorCollectionDTO
{
    /**
     * @var CollectionInterface[]
     */
    public readonly array $originalCollections;

    public function __construct(
        public readonly array $added,
        public readonly array $updated,
        public readonly array $deleted,
        array $originalCollections
    ) {
        $this->originalCollections = $originalCollections;
    }
}
