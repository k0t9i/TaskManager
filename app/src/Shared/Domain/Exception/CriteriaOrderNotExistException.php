<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class CriteriaOrderNotExistException extends DomainException
{
    public function __construct(string $field)
    {
        $message = sprintf(
            'Order field "%s" doesn\'t exist',
            $field
        );
        parent::__construct($message, self::CODE_NOT_FOUND);
    }
}
