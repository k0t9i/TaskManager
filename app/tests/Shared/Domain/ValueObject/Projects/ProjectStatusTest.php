<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain\ValueObject\Projects;

use App\Shared\Domain\ValueObject\Projects\ActiveProjectStatus;
use App\Shared\Domain\ValueObject\Projects\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\Projects\ProjectStatus;
use PHPUnit\Framework\TestCase;

final class ProjectStatusTest extends TestCase
{
    public function testGetScalar(): void
    {
        self::assertEquals(ProjectStatus::STATUS_CLOSED, (new ClosedProjectStatus())->getScalar());
        self::assertEquals(ProjectStatus::STATUS_ACTIVE, (new ActiveProjectStatus())->getScalar());
    }

    public function testCreateFromScalar(): void
    {
        self::assertInstanceOf(
            ClosedProjectStatus::class,
            ProjectStatus::createFromScalar(ProjectStatus::STATUS_CLOSED)
        );
        self::assertInstanceOf(
            ActiveProjectStatus::class,
            ProjectStatus::createFromScalar(ProjectStatus::STATUS_ACTIVE)
        );
    }

    public function testIsClosed(): void
    {
        self::assertTrue((new ClosedProjectStatus())->isClosed());
        self::assertFalse((new ActiveProjectStatus())->isClosed());
    }

    public function testAllowsModification(): void
    {
        self::assertTrue((new ActiveProjectStatus())->allowsModification());
        self::assertFalse((new ClosedProjectStatus())->allowsModification());
    }

    public function testCanBeChangedTo(): void
    {
        $active = new ActiveProjectStatus();
        $closed = new ClosedProjectStatus();

        self::assertTrue($active->canBeChangedTo($closed));
        self::assertFalse($active->canBeChangedTo($active));
        self::assertTrue($closed->canBeChangedTo($active));
        self::assertFalse($closed->canBeChangedTo($closed));
    }
}
