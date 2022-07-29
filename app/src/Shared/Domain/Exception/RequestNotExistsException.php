<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class RequestNotExistsException extends DomainException
{
    public function __construct(string $id)
    {
        $message = sprintf(
            'Request "%s" doesn\'t exist',
            $id
        );
        parent::__construct($message, self::CODE_NOT_FOUND);
    }
}