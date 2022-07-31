<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Infrastructure\Service;

use App\Shared\Application\Service\PasswordHasherInterface;
use App\Shared\Infrastructure\Service\SymfonyPasswordHasher;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\PasswordHasherInterface as SymfonyPasswordHasherInterface;

final class SymfonyPasswordHasherTest extends TestCase
{
    private SymfonyPasswordHasherInterface|MockObject $symfonyHasher;
    private PasswordHasherInterface $hasher;
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();

        $this->symfonyHasher = self::getMockForAbstractClass(
            SymfonyPasswordHasherInterface::class,
            callOriginalConstructor: false,
            mockedMethods: ['hash', 'verify']
        );

        $this->hasher = new SymfonyPasswordHasher($this->symfonyHasher);
    }

    public function testVerifyPassword(): void
    {
        $value = $this->faker->regexify('.{20}');
        $return = $this->faker->regexify('.{20}');
        $this->symfonyHasher
            ->expects(static::once())
            ->method('hash')
            ->with($value)
            ->willReturn($return);

        self::assertEquals($return, $this->hasher->hashPassword($value));
    }

    public function testHashPassword(): void
    {
        $hashed = $this->faker->regexify('.{20}');
        $plain = $this->faker->regexify('.{20}');
        $return = true;
        $this->symfonyHasher
            ->expects(static::once())
            ->method('verify')
            ->with($hashed, $plain)
            ->willReturn($return);

        self::assertEquals($return, $this->hasher->verifyPassword($hashed, $plain));
    }
}
