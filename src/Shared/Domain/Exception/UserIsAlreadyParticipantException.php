<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class UserIsAlreadyParticipantException extends DomainException
{
    public function __construct(string $userId)
    {
        $message = sprintf(
            'User "%s" is already participant',
            $userId
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}