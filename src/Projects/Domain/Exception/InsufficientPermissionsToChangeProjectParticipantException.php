<?php
declare(strict_types=1);

namespace App\Projects\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class InsufficientPermissionsToChangeProjectParticipantException extends DomainException
{

}