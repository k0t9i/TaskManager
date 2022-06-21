<?php
declare(strict_types=1);

namespace App\Projects\Domain\Entity;

use App\Projects\Domain\ValueObject\ProjectRequestChangeDate;
use App\Projects\Domain\ValueObject\ProjectRequestId;
use App\Projects\Domain\ValueObject\ProjectRequestStatus;
use App\Projects\Domain\ValueObject\ProjectRequestUser;

final class ProjectRequest
{
    public function __construct(
        public readonly ProjectRequestId $id,
        public readonly Project $project,
        public readonly ProjectRequestUser $user,
        public readonly ProjectRequestStatus $status,
        public readonly ProjectRequestChangeDate $changeDate
    ) {
    }
}
