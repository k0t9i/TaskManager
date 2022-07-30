<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Collection;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\Exception\LogicException;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testExceptionWhenCreateWithInvalidType(): void
    {
        $items = [
            new CollectionItem('1'),
            22,
            'sssss',
            new CollectionItem('2'),
        ];
        self::expectException(LogicException::class);
        self::expectExceptionMessage(sprintf('Object must be of type "%s"', Hashable::class));
        new TestedCollection($items);
    }

    public function testExceptionWhenCreateWithWrongHashable(): void
    {
        $hashable = new class() implements Hashable {
            public function getHash(): string
            {
                return '';
            }

            public function isEqual(object $other): bool
            {
                return true;
            }
        };

        $items = [
            new CollectionItem('1'),
            new CollectionItem('2'),
            $hashable,
        ];
        self::expectException(LogicException::class);
        self::expectExceptionMessage(sprintf('Object must be of type "%s"', CollectionItem::class));
        new TestedCollection($items);
    }

    public function testExists(): void
    {
        $exists = new CollectionItem('1');
        $notExists = new CollectionItem('2');
        $items = [
            new CollectionItem('3'),
            $exists,
            new CollectionItem('5'),
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
            new CollectionItem('5'),
        ];
        $collection = new TestedCollection($items);
        self::assertTrue($collection->hashExists($exists->getHash()));
        self::assertFalse($collection->hashExists($notExists->getHash()));
    }

    public function testGet(): void
    {
        $item = new CollectionItem('1');
        $items = [
            new CollectionItem('3'),
            $item,
            new CollectionItem('5'),
        ];
        $collection = new TestedCollection($items);
        self::assertSame($collection->get('1'), $item);
    }

    public function testAdd(): void
    {
        $item = new CollectionItem('1');
        $items = [
            new CollectionItem('3'),
            new CollectionItem('5'),
        ];
        $collection = new TestedCollection($items);
        $newCollection = $collection->add($item);
        self::assertNotSame($newCollection, $collection);
        self::assertCount(2, $collection);
        self::assertCount(3, $newCollection);
    }

    public function testRemove(): void
    {
        $item = new CollectionItem('1');
        $items = [
            new CollectionItem('3'),
            new CollectionItem('5'),
            $item,
        ];
        $collection = new TestedCollection($items);
        $newCollection = $collection->remove($item);
        self::assertNotSame($newCollection, $collection);
        self::assertCount(3, $collection);
        self::assertCount(2, $newCollection);
    }
}
