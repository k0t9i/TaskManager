<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

interface UuidGeneratorInterface
{
    public function generate(): string;
}
