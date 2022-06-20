<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure;

use App\Shared\Domain\UuidGeneratorInterface;

class StubUuidGenerator implements UuidGeneratorInterface
{

    public function generate(): string
    {
        // TODO: Implement generate() method.
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
}