<?php
declare(strict_types=1);

namespace App\Tests\unit\Users\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Users\Domain\ValueObject\UserEmail;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class UserEmailTest extends TestCase
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
        $random = $this->faker->email();

        $randomObject = new UserEmail($random);

        self::assertEquals($random, $randomObject->value);
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('User email cannot be blank.');
        new UserEmail('');
    }

    public function testInvalidEmail(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('User email is not a valid email address.');
        new UserEmail($this->faker->regexify('.{255}'));
    }
}

