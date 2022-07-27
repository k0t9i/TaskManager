<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Proxy;

interface DoctrineProxyCollectionItemInterface
{
    public function getKey(): string;
}
