<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Proxy;

interface DoctrineVersionedProxyInterface extends DoctrineProxyInterface
{
    public function getVersion(): int;
}
