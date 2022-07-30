<?php

declare(strict_types=1);

namespace App\Tasks\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class TaskStartDateGreaterThanProjectFinishDateException extends DomainException
{
    public function __construct(string $projectFinishDate, string $startDate)
    {
        $message = sprintf(
            'Task start date "%s" greater than project finish date "%s"',
            $startDate,
            $projectFinishDate
        );
        parent::__construct($message, self::CODE_UNPROCESSABLE_ENTITY);
    }
}
