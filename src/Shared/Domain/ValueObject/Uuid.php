<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use InvalidArgumentException;
use Stringable;

abstract class Uuid implements Stringable
{
    public function __construct(public readonly string $value)
    {
        $this->ensureIsValidUuid($this->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function ensureIsValidUuid(string $value): void
    {
        $pattern = '/\A[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}\z/Dms';
        if (!preg_match($pattern, $value)) {
            throw new InvalidArgumentException(sprintf('Invalid uuid %s', $value));
        }
    }
}