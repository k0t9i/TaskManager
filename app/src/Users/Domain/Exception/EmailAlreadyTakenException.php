<?php

declare(strict_types=1);

namespace App\Users\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class EmailAlreadyTakenException extends DomainException
{
    public function __construct(string $email)
    {
        $message = sprintf(
            'Email "%s" already taken',
            $email
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}
