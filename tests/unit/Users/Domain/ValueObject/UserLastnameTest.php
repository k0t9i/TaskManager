<?php
declare(strict_types=1);

namespace App\Tests\unit\Users\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Users\Domain\ValueObject\UserLastname;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class UserLastnameTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/*']);
        BypassFinals::enable();
        $this->faker = Factory::create();
    }

    public function testValid(): void
    {
        $random = $this->faker->text(255);
        $maxPossible = $this->faker->regexify('.{255}');

        $randomObject = new UserLastname($random);
        $maxPossibleObject = new UserLastname($maxPossible);

        self::assertEquals($random, $randomObject->value);
        self::assertEquals($maxPossible, $maxPossibleObject->value);
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('User lastname cannot be blank.');
        new UserLastname('');
    }

    public function testInvalidMaxLength(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('User lastname should contain at most 255 characters.');
        new UserLastname($this->faker->regexify('.{256}'));
    }
}

