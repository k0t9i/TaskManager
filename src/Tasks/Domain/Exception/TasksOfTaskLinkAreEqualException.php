<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class TasksOfTaskLinkAreEqualException extends DomainException
{
    public function __construct(string $id)
    {
        $message = sprintf(
            'Link task "%" to itself is forbidden',
            $id
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}
