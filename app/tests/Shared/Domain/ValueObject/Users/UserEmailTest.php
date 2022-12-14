<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain\ValueObject\Users;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\Users\UserEmail;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class UserEmailTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testValid(): void
    {
        $random = $this->faker->email();

        $randomObject = new UserEmail($random);

        self::assertEquals($random, $randomObject->value);
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"User email" cannot be blank.');
        new UserEmail('');
    }

    public function testInvalidEmail(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"User email" is not a valid email address.');
        new UserEmail($this->faker->regexify('.{255}'));
    }
}
