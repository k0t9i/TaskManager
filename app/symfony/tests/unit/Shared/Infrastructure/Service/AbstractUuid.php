<?php
declare(strict_types=1);

namespace App\Tests\unit\Shared\Infrastructure\Service;

use Ramsey\Uuid\UuidInterface;

// due to depreciation notice
abstract class AbstractUuid implements UuidInterface
{
    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
    }
}
