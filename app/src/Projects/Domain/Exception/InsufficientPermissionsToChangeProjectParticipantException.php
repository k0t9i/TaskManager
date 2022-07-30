<?php

declare(strict_types=1);

namespace App\Projects\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class InsufficientPermissionsToChangeProjectParticipantException extends DomainException
{
    public function __construct(string $userId, string $projectId)
    {
        $message = sprintf(
            'Insufficient permissions to change participant %s of project "%s"',
            $userId,
            $projectId
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}
