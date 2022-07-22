<?php
declare(strict_types=1);

namespace App\Shared\SharedBoundedContext\Domain\Entity;

use App\Shared\Domain\Aggregate\AggregateRoot;

final class SharedUser extends AggregateRoot
{
    public function __construct(
        private string $id
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }
}