<?php
declare(strict_types=1);

namespace App\Shared\Domain\Service;

interface UuidGeneratorInterface
{
    public function generate(): string;
}