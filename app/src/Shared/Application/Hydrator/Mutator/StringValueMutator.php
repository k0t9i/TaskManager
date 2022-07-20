<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Mutator;

final class StringValueMutator implements ValueMutatorInterface
{
    private readonly ValueMutatorInterface $chainValueMutator;

    public function __construct(string $propertyName)
    {
        $this->chainValueMutator = new ChainValueMutator(
            new PropertyValueMutator($propertyName),
            new PropertyValueMutator('value')
        );
    }

    public function setValue(object $object, mixed $value): void
    {
        $this->chainValueMutator->setValue($object, $value);
    }

    public function getOrCreateObject(object $object): ?object
    {
        return $this->chainValueMutator->getOrCreateObject($object);
    }
}
