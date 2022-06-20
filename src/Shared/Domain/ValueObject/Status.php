<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidNextStatusException;
use App\Shared\Domain\Exception\ModificationDeniedException;

abstract class Status
{
    public function canBeChangedTo(self $status): bool
    {
        return in_array(get_class($status), $this->getNextStatuses(), true);
    }

    abstract public function allowsModification(): bool;

    public function ensureCanBeChangedTo(self $status): void
    {
        if (!$this->canBeChangedTo($status)) {
            throw new InvalidNextStatusException(get_class($status));
        }
    }

    public function ensureAllowsModification(): void
    {
        if (!$this->allowsModification()) {
            throw new ModificationDeniedException(get_class($this));
        }
    }

    /**
     * @return string[]
     */
    abstract protected function getNextStatuses(): array;
}