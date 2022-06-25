<?php
declare(strict_types=1);

namespace App\TaskManagers\Infrastructure\Persistence\Doctrine\Type;

use App\TaskManagers\Domain\ValueObject\TaskId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

class TaskIdType extends StringType
{
    private const NAME = 'taskId';


    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): TaskId
    {
        $value = parent::convertToPHPValue($value, $platform);

        return new TaskId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof TaskId) {
            throw new InvalidArgumentException(sprintf('Invalid task id type %s', gettype($value)));
        }
        return $value->value;
    }
}