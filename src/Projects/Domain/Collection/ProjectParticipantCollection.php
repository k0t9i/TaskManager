<?php
declare(strict_types=1);

namespace App\Projects\Domain\Collection;

use App\Projects\Domain\ValueObject\ProjectParticipant;
use App\Shared\Domain\Collection\Collection;

final class ProjectParticipantCollection extends Collection
{
    protected function getType(): string
    {
        return ProjectParticipant::class;
    }
}
