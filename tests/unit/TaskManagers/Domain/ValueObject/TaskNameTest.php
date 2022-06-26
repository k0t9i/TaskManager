<?php
declare(strict_types=1);

namespace App\Tests\unit\TaskManagers\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\TaskManagers\Domain\ValueObject\TaskName;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class TaskNameTest extends TestCase
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

        $randomObject = new TaskName($random);
        $maxPossibleObject = new TaskName($maxPossible);

        self::assertEquals($random, $randomObject->value);
        self::assertEquals($maxPossible, $maxPossibleObject->value);
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Task name cannot be blank.');
        new TaskName('');
    }

    public function testInvalidMaxLength(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Task name should contain at most 255 characters.');
        new TaskName($this->faker->regexify('.{256}'));
    }
}

