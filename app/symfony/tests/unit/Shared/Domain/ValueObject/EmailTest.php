<?php
declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\Email;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testValid(): void
    {
        $random = $this->faker->email();

        $randomObject = new Email($random);

        self::assertEquals($random, $randomObject->value);
    }

    public function testInvalidEmail(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"Email" is not a valid email address.');
        new Email($this->faker->regexify('.{255}'));
    }
}

