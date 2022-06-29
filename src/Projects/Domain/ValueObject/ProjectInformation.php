<?php
declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Shared\Domain\ValueObject\DateTime;

final class ProjectInformation
{
    public function __construct(
        public readonly ProjectName $name,
        public readonly ProjectDescription $description,
        public readonly DateTime $finishDate,
    ) {
    }
}
