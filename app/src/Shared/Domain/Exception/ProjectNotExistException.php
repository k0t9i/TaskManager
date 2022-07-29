<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class ProjectNotExistException extends DomainException
{
    public function __construct(?string $projectId = null)
    {
        $message = $projectId !== null ? sprintf(
            'Project "%s" doesn\'t exist',
            $projectId
        ) : 'Project doesn\'t exist';
        parent::__construct($message, self::CODE_NOT_FOUND);
    }
}