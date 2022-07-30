<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Proxy;

interface DoctrineVersionedProxyInterface
{
    public function getVersion(): int;
}
