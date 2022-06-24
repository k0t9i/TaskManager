<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Projects\Domain\Factory\ProjectStatusFactory;
use App\Shared\Domain\ValueObject\Status;

abstract class ProjectStatus extends Status
{
    public function getScalar(): int
    {
        return ProjectStatusFactory::scalarFromObject($this);
    }
}