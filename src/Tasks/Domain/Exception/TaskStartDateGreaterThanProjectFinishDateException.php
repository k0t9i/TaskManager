<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Exception;

use DomainException;

class TaskStartDateGreaterThanProjectFinishDateException extends DomainException
{

}
