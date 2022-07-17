<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Accessor;

final class DateValueAccessor implements ValueAccessorInterface
{
    private readonly ValueAccessorInterface $chainValueAccessor;

    public function __construct(string $propertyName)
    {
        $this->chainValueAccessor = new ChainValueAccessor(
            new PropertyValueAccessor($propertyName),
            new MethodValueAccessor('getValue')
        );
    }

    public function getValue(?object $object = null): mixed
    {
        return $this->chainValueAccessor->getValue($object);
    }
}
