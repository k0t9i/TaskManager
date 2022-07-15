<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Accessor;

final class ChainValueAccessor implements ValueAccessorInterface
{
    /**
     * @var ValueAccessorInterface[]
     */
    private readonly array $accessors;

    public function __construct(ValueAccessorInterface... $accessors)
    {
        $this->accessors = $accessors;
    }

    public function getValue(?object $object = null): mixed
    {
        $currentObject = $object;
        foreach ($this->accessors as $accessors) {
            $currentObject = $accessors->getValue($currentObject);
        }
        return $currentObject;
    }
}
