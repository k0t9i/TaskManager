<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

interface DoctrineVersionedProxyInterface
{
    public function getVersion(): int;
}
