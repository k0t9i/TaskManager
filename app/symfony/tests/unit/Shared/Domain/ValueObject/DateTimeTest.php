<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\DateTime;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    public function testInvalidDate(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::getMockForAbstractClass(
            DateTime::class,
            ['invalid date']
        );
    }

    public function testIsGreaterThan(): void
    {
        $date = self::getMockForAbstractClass(
            DateTime::class,
            ['01-01-1990']
        );
        $sameDate = self::getMockForAbstractClass(
            DateTime::class,
            ['01-01-1990']
        );
        $biggerDate = self::getMockForAbstractClass(
            DateTime::class,
            ['02-01-1990']
        );
        self::assertTrue($biggerDate->isGreaterThan($date));
        self::assertFalse($date->isGreaterThan($biggerDate));
        self::assertFalse($date->isGreaterThan($sameDate));
        self::assertFalse($sameDate->isGreaterThan($date));
    }

    public function testIsEqual(): void
    {
        $date = self::getMockForAbstractClass(
            DateTime::class,
            ['01-01-1990 00:01:59.376044']
        );
        $sameDate = self::getMockForAbstractClass(
            DateTime::class,
            ['01-01-1990 00:01:59.376044']
        );
        $biggerDate = self::getMockForAbstractClass(
            DateTime::class,
            ['01-01-1990 00:01:59.376040']
        );
        self::assertFalse($biggerDate->isEqual($date));
        self::assertFalse($date->isEqual($biggerDate));
        self::assertTrue($date->isEqual($sameDate));
        self::assertTrue($sameDate->isEqual($date));
    }
}

