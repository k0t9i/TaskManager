<?php
declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

class TaskBrief
{
    public function __construct(public readonly string $value)
    {
    }
}