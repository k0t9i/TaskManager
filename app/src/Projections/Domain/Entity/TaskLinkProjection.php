<?php

declare(strict_types=1);

namespace App\Projections\Domain\Entity;

final class TaskLinkProjection
{
    public function __construct(
        private string $id,
        private string $toId
    ) {
    }
}
