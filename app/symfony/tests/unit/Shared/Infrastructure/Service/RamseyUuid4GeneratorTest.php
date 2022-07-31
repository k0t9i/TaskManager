<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Infrastructure\Service;

use App\Shared\Application\Service\UuidGeneratorInterface;
use App\Shared\Infrastructure\Service\RamseyUuid4Generator;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

final class RamseyUuid4GeneratorTest extends TestCase
{
    private UuidInterface|MockObject $uuid;
    private UuidGeneratorInterface $generator;
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();

        $this->uuid = self::getMockForAbstractClass(
            AbstractUuid::class,
            callOriginalConstructor: false,
            mockedMethods: ['toString']
        );

        $this->generator = new RamseyUuid4Generator($this->uuid);
    }

    public function testGenerate(): void
    {
        $return = $this->faker->regexify('.{20}');
        $this->uuid
            ->expects(static::once())
            ->method('toString')
            ->willReturn($return);

        self::assertEquals($return, $this->generator->generate());
    }
}
