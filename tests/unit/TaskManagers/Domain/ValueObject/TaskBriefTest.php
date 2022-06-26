<?php
declare(strict_types=1);

namespace App\Tests\unit\TaskManagers\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\TaskManagers\Domain\ValueObject\TaskBrief;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class TaskBriefTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testValid(): void
    {
        $empty = '';
        $random = $this->faker->text(2000);
        $maxPossible = $this->faker->regexify('.{2000}');

        $emptyObject = new TaskBrief($empty);
        $randomObject = new TaskBrief($random);
        $maxPossibleObject = new TaskBrief($maxPossible);

        self::assertEquals($empty, $emptyObject->value);
        self::assertEquals($random, $randomObject->value);
        self::assertEquals($maxPossible, $maxPossibleObject->value);
    }

    public function testInvalidMaxLength(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('Task brief should contain at most 2000 characters.');
        new TaskBrief($this->faker->regexify('.{2001}'));
    }
}

