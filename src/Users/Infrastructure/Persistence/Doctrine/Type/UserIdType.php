<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Persistence\Doctrine\Type;

use App\Shared\Domain\Exception\InvalidArgumentException;
use App\Shared\Domain\ValueObject\UserId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class UserIdType extends StringType
{
    private const NAME = 'userId';


    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): UserId
    {
        $value = parent::convertToPHPValue($value, $platform);

        return new UserId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        if (!$value instanceof UserId) {
            throw new InvalidArgumentException(sprintf('Invalid user id type %s', gettype($value)));
        }
        return $value->value;
    }
}