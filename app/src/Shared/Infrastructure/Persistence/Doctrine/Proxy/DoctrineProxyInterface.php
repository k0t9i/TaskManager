<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;

interface DoctrineProxyInterface
{
    public function refresh(PersistentCollectionLoaderInterface $loader): void;
}
