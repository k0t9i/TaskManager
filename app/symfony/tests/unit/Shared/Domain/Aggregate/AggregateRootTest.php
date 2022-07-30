<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Aggregate;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Event\DomainEvent;
use PHPUnit\Framework\TestCase;

class AggregateRootTest extends TestCase
{
    public function testReleaseEvents(): void
    {
        $values = [
            $this->getMockForAbstractClass(DomainEvent::class, callOriginalConstructor: false),
            $this->getMockForAbstractClass(DomainEvent::class, callOriginalConstructor: false),
            $this->getMockForAbstractClass(DomainEvent::class, callOriginalConstructor: false),
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

