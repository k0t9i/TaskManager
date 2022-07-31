<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Proxy;

interface DoctrineProxyCollectionItemInterface extends DoctrineProxyInterface
{
    public function getKey(): string;
}
