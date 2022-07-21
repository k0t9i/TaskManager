<?php
declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidArgumentException;
use DateTimeImmutable;
use Exception;
use Stringable;

class DateTime implements Stringable
{
    //ATOM with microseconds
    private const DEFAULT_FORMAT = 'Y-m-d\TH:i:s.uP';

    private DateTimeImmutable $dateTime;

    public function __construct(string $value = null)
    {
        try {
            if ($value) {
                $this->dateTime = new DateTimeImmutable($value);
            } else {
                $this->dateTime = DateTimeImmutable::createFromFormat(
                    'U.u',
                    sprintf('%.f', microtime(true))
                );
            }
        } catch (Exception $e) {
            throw new InvalidArgumentException(sprintf('Invalid datetime value "%s"', $value));
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

    public function isEqual(self $other): bool
    {
        return $this->getValue() === $other->getValue();
    }
}