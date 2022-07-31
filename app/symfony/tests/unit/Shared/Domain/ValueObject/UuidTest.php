<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\Uuid;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class UuidTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testValidUuid(): void
    {
        for ($i = 0; $i < 10; ++$i) {
            $value = $this->faker->uuid();
            $uuid = self::getMockForAbstractClass(
                Uuid::class,
                [$value]
            );
            self::assertEquals($value, $uuid->value);
        }
    }

    public function testInvalidUuid(): void
    {
        $value = $this->faker->regexify('.{20}');
        self::expectException(InvalidArgumentException::class);
        self::getMockForAbstractClass(
            Uuid::class,
            [$value]
        );
    }

    public function testIsEqual(): void
    {
        $value = $this->faker->uuid();
        $uuid = self::getMockForAbstractClass(
            Uuid::class,
            [$value]
        );
        $equals = self::getMockForAbstractClass(
            Uuid::class,
            [$value]
        );
        $notEquals = self::getMockForAbstractClass(
            Uuid::class,
            [$this->faker->uuid()]
        );
        $otherClass = self::getMockForAbstractClass(
            Uuid::class,
            [$value],
            mockClassName: 'OtherClass'
        );

        self::assertTrue($uuid->isEqual($equals));
        self::assertTrue($equals->isEqual($uuid));
        self::assertFalse($uuid->isEqual($notEquals));
        self::assertFalse($notEquals->isEqual($uuid));
        self::assertFalse($uuid->isEqual($otherClass));
        self::assertFalse($otherClass->isEqual($uuid));
    }

    public function testCreateFrom(): void
    {
        $uuid = self::getMockForAbstractClass(
            Uuid::class,
            [$this->faker->uuid()]
        );
        /** @psalm-var TestedUuid $otherUuid */
        $otherUuid = TestedUuid::createFrom($uuid);
        self::assertInstanceOf(TestedUuid::class, $otherUuid);
        self::assertEquals($uuid->value, $otherUuid->value);
    }
}
