<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

use ArrayIterator;
use InvalidArgumentException;
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
    public function count(): int
    {
        return count($this->items);
    }

    public function get(string $key): Hashable
    {
        return $this->items[$key];
    }

    public function add(Hashable $item): void
    {
        $this->ensureIsValidType($item);
        $key = $item->getHash();
        if (!isset($this->items[$key]) && !isset($this->deleted[$key])) {
            $this->added[$key] = $item;
        }
        $this->items[$key] = $item;
        unset($this->deleted[$key]);
    }

    public function remove(Hashable $item): void
    {
        $key = $item->getHash();
        if (isset($this->items[$key]) && !isset($this->added[$key])) {
            $this->deleted[$key] = $this->items[$key];
        }
        unset($this->items[$key]);
        unset($this->added[$key]);
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
