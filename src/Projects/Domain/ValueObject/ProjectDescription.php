<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

class ProjectDescription
{
    public function __construct(public readonly string $value)
    {
    }
}