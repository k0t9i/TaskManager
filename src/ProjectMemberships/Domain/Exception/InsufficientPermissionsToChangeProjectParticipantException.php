<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Domain\Exception;

use DomainException;

final class InsufficientPermissionsToChangeProjectParticipantException extends DomainException
{

}