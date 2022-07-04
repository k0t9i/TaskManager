<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Factory\ProjectStatusFactory;

abstract class ProjectStatus extends Status
{
    public function getScalar(): int
    {
        return ProjectStatusFactory::scalarFromObject($this);
    }
}