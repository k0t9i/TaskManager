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

    public function testGetValue(): void
    {
        $values = [
            '01-03-1990' => '1990-03-01T00:00:00+00:00',
            '05.12.2020' => '2020-12-05T00:00:00+00:00',
            '02/07/1980' => '1980-02-07T00:00:00+00:00',
        ];

        foreach ($values as $raw => $value) {
            $date = self::getMockForAbstractClass(
                DateTime::class,
                [$raw]
            );
            self::assertEquals($value, $date->getValue());
        }
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
}

