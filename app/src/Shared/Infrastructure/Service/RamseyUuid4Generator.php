<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Service\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class RamseyUuid4Generator implements UuidGeneratorInterface
{
    public function __construct(private ?UuidInterface $uuid = null)
    {
        if ($this->uuid === null) {
            $this->uuid = Uuid::uuid4();
        }
    }

    public function generate(): string
    {
        return $this->uuid->toString();
    }
}