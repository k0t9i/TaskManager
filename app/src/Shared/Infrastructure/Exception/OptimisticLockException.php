<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Exception;

use Exception;

final class OptimisticLockException extends Exception
{
    public function __construct(int $actualVersion, int $expectedVersion)
    {
        $message = sprintf(
            'The optimistic lock failed, version %s was expected, but is actually %s',
            $expectedVersion, $actualVersion
        );
        parent::__construct($message);
    }
}
