<?php
declare(strict_types=1);

namespace App\Users\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class EmailAlreadyTakenException extends DomainException
{

}
