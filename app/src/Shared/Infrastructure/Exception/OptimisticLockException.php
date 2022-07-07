<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Exception;

use Exception;

final class OptimisticLockException extends Exception
{
    public function __construct(string $aggregateRootId, int $actualVersion, int $expectedVersion)
    {
        $message = sprintf(
            'The optimistic lock failed for aggregate root %s, version %s was expected, but is actually %s',
            $aggregateRootId, $expectedVersion, $actualVersion
        );
        parent::__construct($message);
    }
}
