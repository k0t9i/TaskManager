<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Service\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;

class RamseyUuid4Generator implements UuidGeneratorInterface
{

    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}