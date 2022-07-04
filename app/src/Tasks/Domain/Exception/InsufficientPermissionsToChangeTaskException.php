<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class InsufficientPermissionsToChangeTaskException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Insufficient permissions to change this task', self::CODE_FORBIDDEN);
    }
}
