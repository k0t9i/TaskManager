<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Mutator;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;

final class StatusValueMutator implements ValueMutatorInterface
{
    private readonly PropertyValueMutator $propertyMutator;

    public function __construct(
        private readonly string $propertyName
    ) {
        $this->propertyMutator = new PropertyValueMutator($this->propertyName);
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
        $reflection = new ReflectionClass($property->getType()->getName());
        $value = $reflection->getMethod('createFromScalar')->invoke(null, $value);
        $this->propertyMutator->setValue($object, $value);
    }

    public function getOrCreateObject(object $object): ?object
    {
        return null;
    }
}
