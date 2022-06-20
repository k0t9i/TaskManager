<?php
declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Collection;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    private TestedCollection $collection;

    protected function setUp(): void
    {
        $items = [
            1 => new CollectionItem(),
            4 => new CollectionItem(),
            6 => new CollectionItem(),
            8 => new CollectionItem(),
        ];
        $this->collection = new TestedCollection($items);
    }

    public function testIsDirtyAfterAddition(): void
    {
        $item = new CollectionItem();
        $this->collection[5] = $item;
        self::assertTrue($this->collection->isDirty());
        $items = $this->collection->getAdded();
        self::assertCount(1, $items);
        self::assertTrue(isset($items[5]));
        self::assertTrue($item === $items[5]);
    }

    public function testIsDirtyAfterDeletion(): void
    {
        $item = $this->collection[4];
        unset($this->collection[4]);
        self::assertTrue($this->collection->isDirty());
        $items = $this->collection->getDeleted();
        self::assertCount(1, $items);
        self::assertTrue(isset($items[4]));
        self::assertTrue($item === $items[4]);
    }

    public function testIsNotDirtyAfterAdditionAndDeletionOfSameKey(): void
    {
        $this->collection[9] = new CollectionItem();
        unset($this->collection[9]);
        self::assertFalse($this->collection->isDirty());
        unset($this->collection[1]);
        $this->collection[1] = new CollectionItem();
        self::assertFalse($this->collection->isDirty());
    }

    public function testIsNotDirtyAfterFlush(): void
    {
        $this->collection[9] = new CollectionItem();
        unset($this->collection[1]);
        $this->collection->flush();;
        self::assertFalse($this->collection->isDirty());
    }

    public function testExceptionWhenSetItemWithInvalidType(): void
    {
        self::expectException(InvalidArgumentException::class);
        $this->collection[10] = 1;
    }

    public function testExceptionWhenCreateWithInvalidType(): void
    {
        $items = [
            new CollectionItem(),
            22,
            'sssss',
            new CollectionItem()
        ];
        self::expectException(InvalidArgumentException::class);
        new TestedCollection($items);
    }
}

