<?php
declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Collection;

use App\Shared\Domain\Exception\InvalidArgumentException;
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

