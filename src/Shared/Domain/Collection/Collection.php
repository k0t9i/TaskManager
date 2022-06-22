<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use ArrayIterator;
use InvalidArgumentException;
use Traversable;

abstract class Collection implements CollectionInterface
{
    private array $items = [];
    private array $added = [];
    private array $deleted = [];

    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->ensureIsValidType($item);
        }
        $this->items = $items;
    }

    abstract protected function getType(): string;

    public function getAdded(): array
    {
        return $this->added;
    }

    public function getDeleted(): array
    {
        return $this->deleted;
    }

    public function flush(): void
    {
        $this->added = [];
        $this->deleted = [];
    }

    public function isDirty(): bool
    {
        return count($this->added) > 0 || count($this->deleted) > 0;
    }

    /**
     * @inheritDoc
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->items);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset];
    }

    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->ensureIsValidType($value);
        if (!isset($this->items[$offset]) && !isset($this->deleted[$offset])) {
            $this->added[$offset] = $value;
        }
        $this->items[$offset] = $value;
        unset($this->deleted[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        if (isset($this->items[$offset]) && !isset($this->added[$offset])) {
            $this->deleted[$offset] = $this->items[$offset];
        }
        unset($this->items[$offset]);
        unset($this->added[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->items);
    }

    public function exists(mixed $item): bool
    {
        $this->ensureIsValidType($item);
        return in_array($item, $this->items, true);
    }

    private function ensureIsValidType(mixed $value): void
    {
        if (!is_a($value, $this->getType())) {
            throw new InvalidArgumentException(sprintf('Object must be of type %s', $this->getType()));
        }
    }
}
