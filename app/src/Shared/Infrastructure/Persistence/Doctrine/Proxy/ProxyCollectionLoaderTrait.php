<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\Collection\CollectionInterface;
use Doctrine\ORM\PersistentCollection;
use LogicException;

trait ProxyCollectionLoaderTrait
{
    private function loadCollection(
        CollectionInterface $collection,
        PersistentCollection $persistentCollection,
        DoctrineProxyInterface $owner
    ): void {
        $class = $persistentCollection->getTypeClass()->getName();
        if (!is_a($class, DoctrineProxyCollectionItemInterface::class, true)) {
            throw new LogicException('DoctrineProxyCollectionItemInterface');
        }

        $proxies = $this->prepareProxies($collection, $persistentCollection);

        foreach ($collection->getItems() as $child) {
            $proxy = $proxies[$child->getHash()];
            if ($proxy === null) {
                $proxy = new $class($owner, $child);
                $persistentCollection->add($proxy);
            }
            $proxy->refresh();
        }
        /** @var DoctrineProxyCollectionItemInterface $proxy */
        foreach ($persistentCollection->toArray() as $key => $proxy) {
            if (!$collection->hashExists($proxy->getKey())) {
                unset($persistentCollection[$key]);
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
