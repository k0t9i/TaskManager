<?php
declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Type;

use App\Tasks\Domain\ValueObject\TaskFinishDate;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateImmutableType;
use InvalidArgumentException;

class TaskFinishDateType extends DateImmutableType
{
    private const NAME = 'taskFinishDate';


    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): TaskFinishDate
    {
        $value = parent::convertToPHPValue($value, $platform);

        return TaskFinishDate::createFromPhpDate($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof TaskFinishDate) {
            throw new InvalidArgumentException(sprintf('Invalid task finish date type %s', gettype($value)));
        }
        return parent::convertToDatabaseValue($value->getPhpDate(), $platform);
    }
}