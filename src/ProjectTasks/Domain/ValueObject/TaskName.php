<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\ValueObject;

final class TaskName
{
    public function __construct(public readonly string $value)
    {
    }
}