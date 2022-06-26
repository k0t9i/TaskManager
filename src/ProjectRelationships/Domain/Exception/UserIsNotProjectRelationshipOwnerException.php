<?php
declare(strict_types=1);

namespace App\ProjectRelationships\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

final class UserIsNotProjectRelationshipOwnerException extends DomainException
{

}
