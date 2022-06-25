<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\ValueObject;

final class TaskName
{
    public function __construct(public readonly string $value)
    {
    }
}