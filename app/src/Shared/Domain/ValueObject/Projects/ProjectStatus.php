<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject\Projects;

use App\Shared\Domain\Exception\LogicException;
use App\Shared\Domain\ValueObject\Status;

abstract class ProjectStatus extends Status
{
    private const STATUS_CLOSED = 0;
    private const STATUS_ACTIVE = 1;

    public function getScalar(): int
    {
        if ($this instanceof ClosedProjectStatus) {
            return self::STATUS_CLOSED;
        }
        if ($this instanceof ActiveProjectStatus) {
            return self::STATUS_ACTIVE;
        }

        throw new LogicException(sprintf('Invalid type "%s" of project status', gettype($this)));
    }

    public static function createFromScalar(int $status): static
    {
        if ($status === self::STATUS_CLOSED) {
            return new ClosedProjectStatus();
        }
        if ($status === self::STATUS_ACTIVE) {
            return new ActiveProjectStatus();
        }

        throw new LogicException(sprintf('Invalid project status "%s"', gettype($status)));
    }

    public function isClosed(): bool
    {
        return $this->getScalar() === self::STATUS_CLOSED;
    }
}