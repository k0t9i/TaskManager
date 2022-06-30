<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class TaskLinkAlreadyExistsException extends DomainException
{

}
