<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class InvalidArgumentException extends DomainException
{
    public function __construct(string $message)
    {
        parent::__construct($message, self::CODE_UNPROCESSABLE_ENTITY);
    }
}
