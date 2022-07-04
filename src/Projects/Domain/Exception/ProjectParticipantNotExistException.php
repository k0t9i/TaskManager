<?php
declare(strict_types=1);

namespace App\Projects\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class ProjectParticipantNotExistException extends DomainException
{
    public function __construct(string $id)
    {
        $message = sprintf(
            'Participant "%s" doesn\'t exist',
            $id
        );
        parent::__construct($message, self::CODE_NOT_FOUND);
    }
}