<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Accessor;

final class ConstValueAccessor implements ValueAccessorInterface
{
    public function __construct(private readonly mixed $value)
    {
    }

    public function getValue(?object $object = null): mixed
    {
        return $this->value;
    }
}
