<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Mutator;

interface ValueMutatorInterface
{
    public function setValue(object $object, mixed $value): void;
    public function getOrCreateObject(object $object): ?object;
}
