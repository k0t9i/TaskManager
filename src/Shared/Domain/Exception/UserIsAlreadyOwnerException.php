<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class UserIsAlreadyOwnerException extends DomainException
{
    public function __construct(string $userId)
    {
        $message = sprintf(
            'User "%s" is already owner',
            $userId
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}