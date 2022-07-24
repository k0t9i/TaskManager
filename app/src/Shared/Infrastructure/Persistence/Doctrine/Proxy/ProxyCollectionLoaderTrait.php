<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Domain\Collection\CollectionInterface;
use App\Shared\Domain\Collection\Hashable;
use Doctrine\Common\Collections\Collection;

trait ProxyCollectionLoaderTrait
{
    private function loadCollection(
        CollectionInterface $collection,
        Collection $doctrineCollection,
        Hashable $newObject,
        callable $loader
    ): void {
        foreach ($collection as $child) {
            $proxy = $doctrineCollection->filter(function (Hashable $item) use ($child) {
                return $item->isEqual($child);
            })->first();
            $isNew = false;
            if ($proxy === false) {
                $proxy = $newObject;
                $isNew = true;
            }
            $loader($proxy, $this, $child);
            if ($isNew) {
                $doctrineCollection->add($proxy);
            }
        }
        /** @var Hashable $proxy */
        foreach ($doctrineCollection->toArray() as $key => $proxy) {
            if (!$collection->hashExists($proxy->getHash())) {
                unset($doctrineCollection[$key]);
            }
        }
    }
}
