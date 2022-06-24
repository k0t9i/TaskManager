<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use DateTimeImmutable;
use Exception;
use InvalidArgumentException;
use Stringable;

class DateTime implements Stringable
{
    private const DEFAULT_FORMAT = 'c';

    private DateTimeImmutable $dateTime;

    public function __construct(string $value)
    {
        try {
            $this->dateTime = new DateTimeImmutable($value);
        } catch (Exception $e) {
            throw new InvalidArgumentException(sprintf('Invalid datetime value %s', $value), 0, $e);
        }
    }

    public function getValue(): string
    {
        return $this->dateTime->format(self::DEFAULT_FORMAT);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->dateTime > $other->dateTime;
    }

    public function getPhpDate(): DateTimeImmutable
    {
        return $this->dateTime;
    }

    public static function createFromPhpDate(DateTimeImmutable $date): static
    {
        return new static($date->format(self::DEFAULT_FORMAT));
    }
}