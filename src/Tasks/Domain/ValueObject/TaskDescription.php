<?php
declare(strict_types=1);

namespace App\Tasks\Domain\ValueObject;

class TaskDescription
{
    public function __construct(public readonly string $value)
    {
    }
}