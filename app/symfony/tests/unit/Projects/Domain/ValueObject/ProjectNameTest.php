<?php

declare(strict_types=1);

namespace App\Tests\unit\Projects\Domain\ValueObject;

use App\Projects\Domain\ValueObject\ProjectName;
use App\Shared\Domain\Exception\InvalidArgumentException;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class ProjectNameTest extends TestCase
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

        $randomObject = new ProjectName($random);
        $maxPossibleObject = new ProjectName($maxPossible);

        self::assertEquals($random, $randomObject->value);
        self::assertEquals($maxPossible, $maxPossibleObject->value);
    }

    public function testEmpty(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"Project name" cannot be blank.');
        new ProjectName('');
    }

    public function testInvalidMaxLength(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage('"Project name" should contain at most 255 characters.');
        new ProjectName($this->faker->regexify('.{256}'));
    }
}
