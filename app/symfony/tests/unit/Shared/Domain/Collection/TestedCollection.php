<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Collection;

use App\Shared\Domain\Collection\Collection;

class TestedCollection extends Collection
{
    protected function getType(): string
    {
        return CollectionItem::class;
    }
}
