<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Accessor;

use ReflectionException;
use ReflectionObject;

final class MethodValueAccessor implements ValueAccessorInterface
{
    public function __construct(
        private readonly string $methodName
    ) {
    }

    /**
     * @throws ReflectionException
     */
    public function getValue(?object $object = null): mixed
    {
        $reflection = new ReflectionObject($object);
        $method = $reflection->getMethod($this->methodName);
        $method->setAccessible(true);
        return $method->invoke($object);
    }
}
