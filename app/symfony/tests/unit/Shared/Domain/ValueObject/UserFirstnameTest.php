<?php
declare(strict_types=1);

namespace App\Tests\unit\Users\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\Users\UserFirstname;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class UserFirstnameTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testValid(): void
    {
        $random = $this->faker->text(255);
        $maxPossible = $this->faker->regexify('.{255}');

        $randomObject = new UserFirstname($random);
        $maxPossibleObject = new UserFirstname($maxPossible);

        self::assertEquals($random, $randomObject->value);
        self::assertEquals($maxPossible, $maxPossibleObject->value);
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"User firstname" cannot be blank.');
        new UserFirstname('');
    }

    public function testInvalidMaxLength(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"User firstname" should contain at most 255 characters.');
        new UserFirstname($this->faker->regexify('.{256}'));
    }
}

