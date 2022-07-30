<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidNextStatusException;
use App\Shared\Domain\Exception\ModificationDeniedException;
use App\Shared\Domain\ValueObject\Status;
use PHPUnit\Framework\TestCase;

class StatusTest extends TestCase
{
    public function testCanBeChangedTo(): void
    {
        $otherStatus = self::getMockForAbstractClass(Status::class);

        $status = self::getMockForAbstractClass(Status::class);
        $status->method('getNextStatuses')
            ->will(self::returnValue([get_class($otherStatus)]));
        self::assertTrue($status->canBeChangedTo($otherStatus));

        $status = self::getMockForAbstractClass(Status::class);
        $status->method('getNextStatuses')
            ->will(self::returnValue([self::class])); // random class
        self::assertFalse($status->canBeChangedTo($otherStatus));
    }

    public function testEnsureCanBeChangedTo(): void
    {
        $otherStatus = self::getMockForAbstractClass(Status::class);

        $status = self::getMockForAbstractClass(Status::class);
        $status->method('getNextStatuses')
            ->will(self::returnValue([get_class($otherStatus)]));
        $status->ensureCanBeChangedTo($otherStatus);

        $status = self::getMockForAbstractClass(Status::class);
        $status->method('getNextStatuses')
            ->will(self::returnValue([self::class])); // random class
        self::expectException(InvalidNextStatusException::class);
        $status->ensureCanBeChangedTo($otherStatus);
    }

    public function testEnsureAllowsModification(): void
    {
        $status = self::getMockForAbstractClass(Status::class);
        $status->method('allowsModification')
            ->will(self::returnValue(true));
        $status->ensureAllowsModification();

        $status = self::getMockForAbstractClass(Status::class);
        $status->method('allowsModification')
            ->will(self::returnValue(false));
        self::expectException(ModificationDeniedException::class);
        $status->ensureAllowsModification();
    }
}
