<?php
declare(strict_types=1);

namespace App\ProjectTasks\Domain\Exception;

use DomainException;

final class TaskFinishDateGreaterThanProjectFinishDateException extends DomainException
{

}