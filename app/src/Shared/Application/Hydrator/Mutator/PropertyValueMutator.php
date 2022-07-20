<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Mutator;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;

final class PropertyValueMutator implements ValueMutatorInterface
{
    public function __construct(
        private readonly string $propertyName
    ) {
    }

    /**
     * @param object $object
     * @param mixed $value
     * @throws ReflectionException
     */
    public function setValue(object $object, mixed $value): void
    {
        $reflection = new ReflectionObject($object);
        $property = $reflection->getProperty($this->propertyName);
        while ($reflection !== null && $reflection->getName() !== $property->class) {
            $reflection = $reflection->getParentClass();
        }
        $property = $reflection->getProperty($this->propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * @param object $object
     * @return object|null
     * @throws ReflectionException
     */
    public function getOrCreateObject(object $object): ?object
    {
        $reflection = new ReflectionObject($object);
        $property = $reflection->getProperty($this->propertyName);
        if ($property->isInitialized($object)) {
            return $property->getValue($object);
        }
        $type = $property->getType()->getName();
        if (!class_exists($type)) {
            return null;
        }
        $reflection = new ReflectionClass($type);
        return $reflection->newInstanceWithoutConstructor();
    }
}
