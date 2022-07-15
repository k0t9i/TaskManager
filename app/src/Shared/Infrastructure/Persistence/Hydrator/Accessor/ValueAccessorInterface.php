<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Hydrator\Accessor;

interface ValueAccessorInterface
{
    public function getValue(?object $object = null): mixed;
}
