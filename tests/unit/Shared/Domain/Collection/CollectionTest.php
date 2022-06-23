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
            new CollectionItem('1'),
            new CollectionItem('2'),
            new CollectionItem('3'),
            new CollectionItem('4'),
        ];
        $this->collection = new TestedCollection($items);
    }

    public function testIsDirtyAfterAddition(): void
    {
        $item = new CollectionItem('5');
        $this->collection->add($item);
        self::assertTrue($this->collection->isDirty());
        $items = $this->collection->getAdded();
        self::assertCount(1, $items);
        self::assertTrue(isset($items[$item->getHash()]));
        self::assertTrue($item === $items[$item->getHash()]);
    }

    public function testIsDirtyAfterDeletion(): void
    {
        $item = new CollectionItem('4');
        $this->collection->remove($item);
        self::assertTrue($this->collection->isDirty());
        $items = $this->collection->getDeleted();
        self::assertCount(1, $items);
        self::assertTrue(isset($items[$item->getHash()]));
        self::assertTrue($item->getHash() === $items[$item->getHash()]->getHash());
    }

    public function testIsNotDirtyAfterAdditionAndDeletionOfSameKey(): void
    {
        $item = new CollectionItem('9');
        $this->collection->add($item);
        $this->collection->remove($item);
        self::assertFalse($this->collection->isDirty());

        $item = new CollectionItem('1');
        $this->collection->remove($item);
        $this->collection->add(new CollectionItem('1'));
        self::assertFalse($this->collection->isDirty());
    }

    public function testIsNotDirtyAfterFlush(): void
    {
        $this->collection->add(new CollectionItem('9'));
        $this->collection->remove(new CollectionItem('1'));
        $this->collection->flush();;
        self::assertFalse($this->collection->isDirty());
    }

    public function testExceptionWhenCreateWithInvalidType(): void
    {
        $items = [
            new CollectionItem('1'),
            22,
            'sssss',
            new CollectionItem('2')
        ];
        self::expectException(InvalidArgumentException::class);
        new TestedCollection($items);
    }

    public function testExists(): void
    {
        $exists = new CollectionItem('1');
        $notExists = new CollectionItem('2');
        $items = [
            new CollectionItem('3'),
            $exists,
            new CollectionItem('5')
        ];
        $collection = new TestedCollection($items);
        self::assertTrue($collection->exists($exists));
        self::assertFalse($collection->exists($notExists));
    }

    public function testHashExists(): void
    {
        $exists = new CollectionItem('1');
        $notExists = new CollectionItem('2');
        $items = [
            new CollectionItem('3'),
            $exists,
            new CollectionItem('5')
        ];
        $collection = new TestedCollection($items);
        self::assertTrue($collection->hashExists($exists->getHash()));
        self::assertFalse($collection->hashExists($notExists->getHash()));
    }
}

