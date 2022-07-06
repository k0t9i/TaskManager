<?php
declare(strict_types=1);

namespace App\Tests\unit\TaskManagers\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\TaskManagers\Domain\ValueObject\TaskDescription;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class TaskDescriptionTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testValid(): void
    {
        $empty = '';
        $random = $this->faker->text(4000);
        $maxPossible = $this->faker->regexify('.{4000}');

        $emptyObject = new TaskDescription($empty);
        $randomObject = new TaskDescription($random);
        $maxPossibleObject = new TaskDescription($maxPossible);

        self::assertEquals($empty, $emptyObject->value);
        self::assertEquals($random, $randomObject->value);
        self::assertEquals($maxPossible, $maxPossibleObject->value);
    }

    public function testInvalidMaxLength(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Task description should contain at most 4000 characters.');
        new TaskDescription($this->faker->regexify('.{4001}'));
    }
}
