<?php
declare(strict_types=1);

namespace App\TaskManagers\Domain\Exception;

use DomainException;

final class InsufficientPermissionsToChangeTaskManagerTaskException extends DomainException
{

}
