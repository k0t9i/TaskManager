<?php
declare(strict_types=1);

namespace App\Tests\unit\Projects\Domain\ValueObject;

use App\Projects\Domain\ValueObject\ProjectDescription;
use App\Shared\Domain\Exception\InvalidArgumentException;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class ProjectDescriptionTest extends TestCase
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

        $emptyObject = new ProjectDescription($empty);
        $randomObject = new ProjectDescription($random);
        $maxPossibleObject = new ProjectDescription($maxPossible);

        self::assertEquals($empty, $emptyObject->value);
        self::assertEquals($random, $randomObject->value);
        self::assertEquals($maxPossible, $maxPossibleObject->value);
    }

    public function testInvalidMaxLength(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"Project description" should contain at most 4000 characters.');
        new ProjectDescription($this->faker->regexify('.{4001}'));
    }
}

