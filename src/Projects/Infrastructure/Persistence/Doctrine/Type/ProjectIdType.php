<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Type;

use App\Projects\Domain\ValueObject\ProjectId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use InvalidArgumentException;

class ProjectIdType extends StringType
{
    private const NAME = 'projectId';


    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ProjectId
    {
        $value = parent::convertToPHPValue($value, $platform);

        return new ProjectId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof ProjectId) {
            throw new InvalidArgumentException(sprintf('Invalid project id type %s', gettype($value)));
        }
        return $value->value;
    }
}