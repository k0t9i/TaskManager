<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\Users\UserId;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class OwnerTest extends TestCase
{
    private Generator $faker;
    private string $ownerId;
    private Owner $owner;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->ownerId = $this->faker->uuid();
        $this->owner = new Owner(new UserId($this->ownerId));
    }

    public function testEnsureIsNotOwner(): void
    {
        self::expectException(UserIsAlreadyOwnerException::class);
        $this->owner->ensureIsNotOwner(new UserId($this->ownerId));
    }

    public function testIsOwner(): void
    {
        self::assertTrue($this->owner->isOwner(new UserId($this->ownerId)));
        self::assertFalse($this->owner->isOwner(new UserId($this->faker->uuid())));
    }

    public function testEnsureIsOwner(): void
    {
        self::expectException(UserIsNotOwnerException::class);
        $this->owner->ensureIsOwner(new UserId($this->faker->uuid()));
    }
}
