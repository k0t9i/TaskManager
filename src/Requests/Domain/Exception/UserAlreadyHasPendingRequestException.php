<?php
declare(strict_types=1);

namespace App\Requests\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class UserAlreadyHasPendingRequestException extends DomainException
{

}