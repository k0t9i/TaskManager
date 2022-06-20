<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Exception;

use DomainException;

final class TaskStartDateGreaterThanFinishDateException extends DomainException
{

}
