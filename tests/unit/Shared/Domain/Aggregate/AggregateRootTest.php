<?php
declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Aggregate;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Bus\Event\DomainEvent;
use PHPUnit\Framework\TestCase;

class AggregateRootTest extends TestCase
{
    public function testReleaseEvents(): void
    {
        $values = [
            new DomainEvent(),
            new DomainEvent(),
            new DomainEvent(),
        ];

        $root = self::getMockForAbstractClass(AggregateRoot::class);
        foreach ($values as $value) {
            $root->registerEvent($value);
        }
        $events = $root->releaseEvents();

        self::assertCount(3, $events);
        self::assertSame($values, $events);
        self::assertEmpty($root->releaseEvents());
    }
}

