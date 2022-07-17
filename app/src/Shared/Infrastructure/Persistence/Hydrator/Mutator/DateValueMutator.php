<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Mutator;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;

final class DateValueMutator implements ValueMutatorInterface
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
        $value = $reflection->newInstance($value);
        $this->propertyMutator->setValue($object, $value);
    }

    /**
     * @param object $object
     * @return object|null
     * @throws ReflectionException
     */
    public function getOrCreateObject(object $object): ?object
    {
        return $this->propertyMutator->getOrCreateObject($object);
    }
}
