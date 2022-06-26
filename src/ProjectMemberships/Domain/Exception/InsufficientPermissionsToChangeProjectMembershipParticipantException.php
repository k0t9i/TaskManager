<?php
declare(strict_types=1);

namespace App\ProjectMemberships\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class InsufficientPermissionsToChangeProjectMembershipParticipantException extends DomainException
{

}