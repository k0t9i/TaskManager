<?php
declare(strict_types=1);

namespace App\Shared\Application\Hydrator\Metadata;

use App\Shared\Application\Hydrator\Accessor\PropertyValueAccessor;
use App\Shared\Application\Hydrator\Mutator\PropertyValueMutator;
use App\Shared\Domain\Service\Utils;
use ReflectionClass;
use ReflectionException;

abstract class ProjectionStorageMetadata extends StorageMetadata
{
    private ?array $storageFields = null;
    private ?array $map = null;

    /**
     * @return StorageMetadataField[]
     * @throws ReflectionException
     */
    public function getStorageFields(?object $parentObject = null): array
    {
        if ($this->storageFields === null) {
            $this->storageFields = [];
            foreach ($this->columnToPropertyMap() as $column => $property) {
                $this->storageFields[$property] = new StorageMetadataField(
                    $column,
                    new PropertyValueAccessor($property),
                    new PropertyValueMutator($property)
                );
            }
        }
        return $this->storageFields;
    }

    /**
     * @throws ReflectionException
     */
    protected function columnToPropertyMap(): array
    {
        if ($this->map === null) {
            $this->map = [];

            $reflection = new ReflectionClass($this->getClassName());
            foreach ($reflection->getProperties() as $property) {
                $this->map[Utils::toSnakeCase($property->name)] = $property->name;
            }
        }
        return $this->map;
    }
}
