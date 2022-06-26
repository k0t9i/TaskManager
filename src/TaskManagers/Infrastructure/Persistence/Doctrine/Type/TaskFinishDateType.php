<?php
declare(strict_types=1);

namespace App\TaskManagers\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\DateTime;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateImmutableType;

class TaskFinishDateType extends DateImmutableType
{
    private const NAME = 'taskFinishDate';


    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): DateTime
    {
        $value = parent::convertToPHPValue($value, $platform);

        return DateTime::createFromPhpDate($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof DateTime) {
            throw new InvalidArgumentException(sprintf('Invalid task finish date type %s', gettype($value)));
        }
        return parent::convertToDatabaseValue($value->getPhpDate(), $platform);
    }
}