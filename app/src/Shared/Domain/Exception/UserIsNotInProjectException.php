<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class UserIsNotInProjectException extends DomainException
{
    public function __construct(string $userId, string $projectId)
    {
        $message = sprintf(
            'User "%s" is not in project "%s"',
            $userId, $projectId
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}