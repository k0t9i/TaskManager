<?php
declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Collection;

use App\Shared\Domain\Collection\Hashable;

class CollectionItem implements Hashable
{
    public function __construct(public readonly string $value)
    {
    }

    public function getHash(): string
    {
        return $this->value;
    }
}