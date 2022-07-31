<?php

declare(strict_types=1);

namespace App\Tests\Shared\Domain\Collection;

use App\Shared\Domain\Collection\Collection;

final class TestedCollection extends Collection
{
    protected function getType(): string
    {
        return CollectionItem::class;
    }
}
