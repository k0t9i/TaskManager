<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Collection\CollectionInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;
use LogicException;

final class PersistentCollectionLoader implements PersistentCollectionLoaderInterface
{
    public function loadInto(Collection $target, CollectionInterface $source, DoctrineProxyInterface $owner): void
    {
        // The collections of newly created parent are empty and not yet wrapped
        if ($target->isEmpty() && !($target instanceof PersistentCollection)) {
            return;
        }
        $class = $target->getTypeClass()->getName();
        if (!is_a($class, DoctrineProxyCollectionItemInterface::class, true)) {
            throw new LogicException('DoctrineProxyCollectionItemInterface');
        }

        $proxies = $this->prepareProxies($source, $target);

        foreach ($source->getItems() as $child) {
            $proxy = $proxies[$child->getHash()];
            if (null === $proxy) {
                // FIXME how do I know that the constructor has these arguments
                $proxy = new $class($owner, $child);
                $target->add($proxy);
            }
            $proxy->refresh($this);
        }
        foreach ($target->toArray() as $key => $proxy) {
            if (!$source->hashExists($proxy->getKey())) {
                unset($target[$key]);
            }
        }
    }

    /**
     * @return DoctrineProxyCollectionItemInterface[]
     */
    private function prepareProxies(CollectionInterface $collection, PersistentCollection $persistentCollection): array
    {
        $proxies = [];
        /** @var DoctrineProxyCollectionItemInterface $item */
        foreach ($persistentCollection as $item) {
            $proxies[$item->getKey()] = $item;
        }
        foreach ($collection->getItems() as $item) {
            if (!isset($proxies[$item->getHash()])) {
                $proxies[$item->getHash()] = null;
            }
        }
        return $proxies;
    }
}
