<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use App\Shared\Domain\Exception\InvalidArgumentException;
use ArrayIterator;
use Traversable;

abstract class Collection implements CollectionInterface
{
    /**
     * @var Hashable[]
     */
    private array $items = [];

    /**
     * @var Hashable[]
     */
    private array $added = [];

    /**
     * @var Hashable[]
     */
    private array $deleted = [];

    /**
     * @var Hashable[]
     */
    private array $updated = [];

    /**
     * @param array|Hashable $items
     */
    public function __construct(array $items = [])
    {
        foreach ($items as $item) {
            $this->ensureIsValidType($item);
            $this->items[$item->getHash()] = $item;
        }
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

    public function getItems(): array
    {
        return $this->items;
    }

    public function getUpdated(): array
    {
        return $this->updated;
    }

    public function flush(): void
    {
        $this->added = [];
        $this->deleted = [];
        $this->updated = [];
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
    public function count(): int
    {
        return count($this->items);
    }

    public function get(string $key): Hashable
    {
        return $this->items[$key];
    }

    public function add(Hashable $item): static
    {
        $this->ensureIsValidType($item);
        $key = $item->getHash();
        $collection = static::createFromOther($this);
        if (!isset($collection->items[$key]) && !isset($collection->deleted[$key])) {
            $collection->added[$key] = $item;
        }
        if (isset($collection->items[$key])) {
            if (!$collection->items[$key]->isEqual($item)) {
                $collection->updated[$key] = $item;
            }
        }
        $collection->items[$key] = $item;
        unset($collection->deleted[$key]);

        return $collection;
    }

    public function remove(Hashable $item): static
    {
        $key = $item->getHash();
        $collection = static::createFromOther($this);
        if (isset($collection->items[$key]) && !isset($collection->added[$key])) {
            $collection->deleted[$key] = $collection->items[$key];
        }
        unset($collection->items[$key]);
        unset($collection->added[$key]);
        unset($collection->updated[$key]);

        return $collection;
    }

    public function exists(Hashable $item): bool
    {
        $this->ensureIsValidType($item);
        return array_key_exists($item->getHash(), $this->items);
    }

    public function hashExists(string $hash): bool
    {
        return array_key_exists($hash, $this->items);
    }

    private static function createFromOther(self $other): static
    {
        $collection = new static();
        $collection->items = $other->items;
        $collection->added = $other->added;
        $collection->deleted = $other->deleted;
        return $collection;
    }

    private function ensureIsValidType(mixed $value): void
    {
        if (!$value instanceof Hashable) {
            throw new InvalidArgumentException(sprintf(
                'Object must be of type %s',
                Hashable::class
            ));
        }
        if (!is_a($value, $this->getType())) {
            throw new InvalidArgumentException(sprintf('Object must be of type %s', $this->getType()));
        }
    }
}
