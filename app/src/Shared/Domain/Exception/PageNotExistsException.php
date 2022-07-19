<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

final class PageNotExistsException extends DomainException
{
    public function __construct(int $page)
    {
        $message = sprintf(
            'Page "%s" doesn\'t exist',
            $page
        );
        parent::__construct($message, self::CODE_NOT_FOUND);
    }
}
