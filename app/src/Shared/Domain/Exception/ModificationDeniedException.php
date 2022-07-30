<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class ModificationDeniedException extends DomainException
{
    public function __construct(string $status)
    {
        $message = sprintf(
            'Modification not allowed when status is "%s"',
            $status
        );
        parent::__construct($message, self::CODE_FORBIDDEN);
    }
}
