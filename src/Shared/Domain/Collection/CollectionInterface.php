<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface CollectionInterface extends Countable, IteratorAggregate, ArrayAccess
{
    public function exists(mixed $item): bool;
    public function getAdded(): array;
    public function getDeleted(): array;
    public function flush(): void;
    public function isDirty(): bool;
}
