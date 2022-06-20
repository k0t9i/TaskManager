<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

final class ProjectName
{
    public function __construct(public readonly string $value)
    {
    }
}