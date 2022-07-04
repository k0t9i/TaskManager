<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class ProjectNotExistException extends DomainException
{
    public function __construct(string $projectId)
    {
        $message = sprintf(
            'Project "%s" doesn\'t exist',
            $projectId
        );
        parent::__construct($message, self::CODE_NOT_FOUND);
    }
}