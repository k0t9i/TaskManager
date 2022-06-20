<?php
declare(strict_types=1);

namespace App\Projects\Infrastructure\Persistence\Doctrine\Type;

use App\Projects\Domain\Factory\ProjectStatusFactory;
use App\Projects\Domain\ValueObject\ProjectStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;

class ProjectStatusType extends IntegerType
{
    private const NAME = 'projectStatus';


    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ProjectStatus
    {
        $value = parent::convertToPHPValue($value, $platform);
        return ProjectStatusFactory::objectFromScalar($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        return ProjectStatusFactory::scalarFromObject($value);
    }
}