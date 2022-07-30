<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\Exception\InvalidArgumentException;
use Stringable;

abstract class Uuid implements Stringable, Hashable
{
    public function __construct(public readonly string $value)
    {
        $this->ensureIsValidUuid($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public static function createFrom(Uuid $other): static
    {
        return new static($other->value);
    }

    public function getHash(): string
    {
        return $this->value;
    }

    public function isEqual(object $other): bool
    {
        return get_class($this) === get_class($other) && $this->getHash() === $other->getHash();
    }

    private function ensureIsValidUuid(string $value): void
    {
        $pattern = '/\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z/Dms';
        if (!preg_match($pattern, $value)) {
            throw new InvalidArgumentException(sprintf('Invalid uuid "%s"', $value));
        }
    }
}
