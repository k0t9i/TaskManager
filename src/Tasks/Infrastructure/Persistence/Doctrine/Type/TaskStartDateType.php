<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Type;

use App\Tasks\Domain\ValueObject\TaskStartDate;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateImmutableType;
use InvalidArgumentException;

class TaskStartDateType extends DateImmutableType
{
    private const NAME = 'taskStartDate';


    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): TaskStartDate
    {
        $value = parent::convertToPHPValue($value, $platform);

        return TaskStartDate::createFromPhpDate($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof TaskStartDate) {
            throw new InvalidArgumentException(sprintf('Invalid task start date type %s', gettype($value)));
        }
        return parent::convertToDatabaseValue($value->getPhpDate(), $platform);
    }
}