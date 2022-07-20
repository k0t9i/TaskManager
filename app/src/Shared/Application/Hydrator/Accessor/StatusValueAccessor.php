<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Accessor;

final class StatusValueAccessor implements ValueAccessorInterface
{
    private readonly ValueAccessorInterface $chainValueAccessor;

    public function __construct(string $propertyName)
    {
        $this->chainValueAccessor = new ChainValueAccessor(
            new PropertyValueAccessor($propertyName),
            new MethodValueAccessor('getScalar')
        );
    }

    public function getValue(?object $object = null): mixed
    {
        return $this->chainValueAccessor->getValue($object);
    }
}
