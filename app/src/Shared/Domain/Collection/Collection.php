<?php

declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use App\Shared\Domain\Exception\LogicException;
use ArrayIterator;
use Traversable;

abstract class Collection implements CollectionInterface
{
    /**
     * @var Hashable[]
     */
    private array $items = [];

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

    public function getItems(): array
    {
        return $this->items;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

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
        $collection->items[$key] = $item;

        return $collection;
    }

    public function remove(Hashable $item): static
    {
        $key = $item->getHash();
        $collection = static::createFromOther($this);
        unset($collection->items[$key]);

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

        return $collection;
    }

    private function ensureIsValidType(mixed $value): void
    {
        if (!$value instanceof Hashable) {
            throw new LogicException(sprintf('Object must be of type "%s"', Hashable::class));
        }
        if (!is_a($value, $this->getType())) {
            throw new LogicException(sprintf('Object must be of type "%s"', $this->getType()));
        }
    }
}
