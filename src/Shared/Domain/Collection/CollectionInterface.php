<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use Countable;
use IteratorAggregate;

interface CollectionInterface extends Countable, IteratorAggregate
{
    public function get(string $key): Hashable;
    public function exists(Hashable $item): bool;
    public function hashExists(string $hash): bool;
    public function add(Hashable $item): static;
    public function remove(Hashable $item): static;
    /**
     * @return Hashable[]
     */
    public function getAdded(): array;

    /**
     * @return Hashable[]
     */
    public function getDeleted(): array;

    /**
     * @return Hashable[]
     */
    public function getItems(): array;

    /**
     * @return Hashable[]
     */
    public function getUpdated(): array;
    public function flush(): void;
    public function isDirty(): bool;
}
