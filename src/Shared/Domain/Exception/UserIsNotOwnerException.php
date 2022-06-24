<?php
declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use DomainException;

final class UserIsNotOwnerException extends DomainException
{

}