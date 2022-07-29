<?php
declare(strict_types=1);

namespace App\Projects\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class UserAlreadyHasPendingRequestException extends DomainException
{
    public function __construct(string $id, string $projectId)
    {
        $message = sprintf(
            'User "%s" already has request to project "%s"',
            $id,
            $projectId
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}