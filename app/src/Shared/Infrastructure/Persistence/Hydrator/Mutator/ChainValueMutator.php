<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Mutator;

final class ChainValueMutator implements ValueMutatorInterface
{
    /**
     * @var ValueMutatorInterface[]
     */
    private readonly array $mutators;

    public function __construct(ValueMutatorInterface... $mutators)
    {
        $this->mutators = $mutators;
    }

    public function setValue(object $object, mixed $value): void
    {
        $objectChain = [$object];
        $prevObject = $object;
        $mutators = $this->mutators;
        $lastMutator = array_pop($mutators);
        foreach ($mutators as $mutator) {
            $prevObject = $mutator->getOrCreateObject($prevObject);
            $objectChain[] = $prevObject;
        }
        $objectChain = array_reverse($objectChain);
        $lastMutator->setValue($objectChain[0], $value);
        foreach (array_reverse($mutators) as $key => $mutator) {
            $currentObject = $objectChain[$key + 1];
            $currentValue = $objectChain[$key];
            $mutator->setValue($currentObject, $currentValue);
        }
    }

    public function getOrCreateObject(object $object): ?object
    {
        $currentObject = $object;
        foreach ($this->mutators as $mutator) {
            $currentObject = $mutator->getOrCreateObject($currentObject);
            if ($currentObject === null) {
                break;
            }
        }
        return $currentObject;
    }
}
