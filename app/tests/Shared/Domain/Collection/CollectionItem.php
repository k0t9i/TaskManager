<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain\Collection;

use App\Shared\Domain\Collection\Hashable;

final class CollectionItem implements Hashable
{
    public function __construct(public readonly string $value)
    {
    }

    public function getHash(): string
    {
        return $this->value;
    }

    public function isEqual(object $other): bool
    {
        return true;
    }
}
