<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class TaskLinkNotExistException extends DomainException
{
    public function __construct(string $fromTaskId, string $toTaskId)
    {
        $message = sprintf(
            'Link from task "%s" to task "%s" does\'t exist',
            $fromTaskId,
            $toTaskId
        );
        parent::__construct($message, self::CODE_NOT_FOUND);
    }
}
