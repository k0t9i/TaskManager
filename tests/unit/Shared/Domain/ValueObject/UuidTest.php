<?php
declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testValidUuid(): void
    {
        $values = [
            '508ca6aa-d3d5-4bab-88d0-80304310d3a8',
            '09d22bcb-0c47-434b-8f82-72ec3ee40121',
            '3f107baa-44ec-4d12-8970-05ad8db7ea60',
            'f11e25a5-682e-44a5-9f9e-47b59cbd7959',
            'a0df9c0f-aee8-4773-9491-83ad3eed19ff',
            'c8b8b42c-48eb-4f9d-8342-187aaa4c5704',
            '79c7f503-fd2d-4beb-91b5-4433cad55fca',
            '8714a8c3-f876-486d-b37e-ff459170fab5',
            'f6c44757-b78b-4cdc-83de-5f134822642f',
            'd18ee56e-3c5b-440a-a471-4012558e6908'
        ];
        foreach ($values as $value) {
            $uuid = self::getMockForAbstractClass(
                Uuid::class,
                [$value]
            );
            self::assertEquals($value, $uuid->value);
        }
    }

    public function testInvalidUuid(): void
    {
        $values = [
            '508ca6aa-d3d5-4bab-88d0-80304310d3a81',
            '09d22bcb-0c47-434b-72ec3ee40121',
            '44ec-4d12-8970-05ad8db7ea60',
            'string',
            'sequence of words',
        ];
        foreach ($values as $value) {
            self::expectException(InvalidArgumentException::class);
            self::getMockForAbstractClass(
                Uuid::class,
                [$value]
            );
        }
    }

    public function testIsEqual(): void
    {
        $uuid = self::getMockForAbstractClass(
            Uuid::class,
            ['d18ee56e-3c5b-440a-a471-4012558e6908']
        );
        $equals = self::getMockForAbstractClass(
            Uuid::class,
            ['d18ee56e-3c5b-440a-a471-4012558e6908']
        );
        $notEquals = self::getMockForAbstractClass(
            Uuid::class,
            ['d18ee56e-9999-440a-a471-4012558e6908']
        );
        $otherClass = self::getMockForAbstractClass(
            Uuid::class,
            ['d18ee56e-3c5b-440a-a471-4012558e6908'],
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
            ['d18ee56e-3c5b-440a-a471-4012558e6908']
        );
        $otherUuid = TestedUuid::createFrom($uuid);
        self::assertInstanceOf(TestedUuid::class, $otherUuid);
        self::assertEquals($uuid->value, $otherUuid->value);
    }
}

