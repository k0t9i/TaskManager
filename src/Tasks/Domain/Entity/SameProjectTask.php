<?php
declare(strict_types=1);

namespace App\Tasks\Domain\Entity;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\TaskId;
use App\Tasks\Domain\ValueObject\TaskName;

final class SameProjectTask implements Hashable
{
    public function __construct(
        private TaskId $id,
        private TaskName $name
    ) {
    }

    public function getId(): TaskId
    {
        return $this->id;
    }

    public function getName(): TaskName
    {
        return $this->name;
    }

    public function getHash(): string
    {
        return $this->id->getHash();
    }
}
