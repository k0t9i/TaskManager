<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\ValueObject\Requests;

use App\Shared\Domain\ValueObject\Requests\ConfirmedRequestStatus;
use App\Shared\Domain\ValueObject\Requests\PendingRequestStatus;
use App\Shared\Domain\ValueObject\Requests\RejectedRequestStatus;
use App\Shared\Domain\ValueObject\Requests\RequestStatus;
use PHPUnit\Framework\TestCase;

final class RequestStatusTest extends TestCase
{
    public function testGetScalar(): void
    {
        self::assertEquals(RequestStatus::STATUS_PENDING, (new PendingRequestStatus())->getScalar());
        self::assertEquals(RequestStatus::STATUS_CONFIRMED, (new ConfirmedRequestStatus())->getScalar());
        self::assertEquals(RequestStatus::STATUS_REJECTED, (new RejectedRequestStatus())->getScalar());
    }

    public function testCreateFromScalar(): void
    {
        self::assertInstanceOf(
            PendingRequestStatus::class,
            RequestStatus::createFromScalar(RequestStatus::STATUS_PENDING)
        );
        self::assertInstanceOf(
            ConfirmedRequestStatus::class,
            RequestStatus::createFromScalar(RequestStatus::STATUS_CONFIRMED)
        );
        self::assertInstanceOf(
            RejectedRequestStatus::class,
            RequestStatus::createFromScalar(RequestStatus::STATUS_REJECTED)
        );
    }

    public function testIsPending(): void
    {
        self::assertTrue((new PendingRequestStatus())->isPending());
        self::assertFalse((new ConfirmedRequestStatus())->isPending());
        self::assertFalse((new RejectedRequestStatus())->isPending());
    }

    public function testIsConfirmed(): void
    {
        self::assertFalse((new PendingRequestStatus())->isConfirmed());
        self::assertTrue((new ConfirmedRequestStatus())->isConfirmed());
        self::assertFalse((new RejectedRequestStatus())->isConfirmed());
    }

    public function testAllowsModification(): void
    {
        self::assertTrue((new PendingRequestStatus())->allowsModification());
        self::assertTrue((new ConfirmedRequestStatus())->allowsModification());
        self::assertTrue((new RejectedRequestStatus())->allowsModification());
    }

    public function testCanBeChangedTo(): void
    {
        $pending = new PendingRequestStatus();
        $confirmed = new ConfirmedRequestStatus();
        $rejected = new RejectedRequestStatus();

        self::assertTrue($pending->canBeChangedTo($confirmed));
        self::assertTrue($pending->canBeChangedTo($rejected));
        self::assertFalse($pending->canBeChangedTo($pending));
        self::assertFalse($confirmed->canBeChangedTo($pending));
        self::assertFalse($confirmed->canBeChangedTo($confirmed));
        self::assertFalse($confirmed->canBeChangedTo($rejected));
        self::assertFalse($rejected->canBeChangedTo($pending));
        self::assertFalse($rejected->canBeChangedTo($confirmed));
        self::assertFalse($rejected->canBeChangedTo($rejected));
    }
}
