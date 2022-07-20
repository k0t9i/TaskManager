<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\CriteriaJoinDTO;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;

final class CriteriaStorageFieldParser implements CriteriaStorageFieldParserInterface
{
    /**
     * @return CriteriaJoinDTO[]
     */
    public function parseJoins(Criteria $criteria, StorageMetadataInterface $metadata): array
    {
        $result = [];
        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as [$type, $operand]) {
            $result = array_merge($result, $this->prepareJoins($operand->property, $metadata));
        }
        foreach ($criteria->getOrders() as $order) {
            $result = array_merge($result, $this->prepareJoins($order->property, $metadata));
        }
        return $result;
    }

    public function parseColumns(array $joins, Criteria $criteria, StorageMetadataInterface $metadata): array
    {
        $result = [];

        if (count($joins) > 0) {
            foreach ($criteria->getExpression()->getOperands() as [$type, $operand]) {
                $result = array_merge($result, $this->prepareColumns($operand->property, $metadata));
            }
            foreach ($criteria->getOrders() as $order) {
                $result = array_merge($result, $this->prepareColumns($order->property, $metadata));
            }
        } else {
            foreach ($metadata->getPropertyToColumnMap() as $property => $metadataField) {
                $result[$property] = $metadataField->name;
            }
        }

        return $result;
    }

    /**
     * @return CriteriaJoinDTO[]
     */
    private function prepareJoins(string $property, StorageMetadataInterface $metadata): array
    {
        $columns = $metadata->getPropertyToColumnMap();
        $parentTable = $metadata->getStorageName();
        $parts = explode('.', $property);

        $result = [];
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            $storageField = $columns[$part];
            if ($storageField->metadata !== null) {
                $joinTable = $storageField->metadata->getStorageName();
                $condition = [];
                foreach ($storageField->metadata->getStorageFields() as $childStorageField) {
                    if ($childStorageField->parentColumn !== null) {
                        $condition[] = $parentTable . '.' . $childStorageField->parentColumn . ' = ' .
                            $joinTable . '.' . $childStorageField->name;
                    }
                }
                $result[$joinTable] = new CriteriaJoinDTO(
                    $parentTable,
                    $joinTable,
                    $joinTable,
                    implode(' AND ', $condition)
                );
                $parentTable = $storageField->metadata->getStorageName();
                $columns = $storageField->metadata->getPropertyToColumnMap();
            }
        }

        return $result;
    }

    private function prepareColumns(string $property, StorageMetadataInterface $metadata): array
    {
        $columns = $metadata->getPropertyToColumnMap();
        $parts = explode('.', $property);

        $result = [
            $property => ''
        ];
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            $storageField = $columns[$part];
            if ($storageField->metadata !== null) {
                $columns = $storageField->metadata->getPropertyToColumnMap();
                $metadata = $storageField->metadata;
            } else {
                $result[$property] = $metadata->getStorageName() . '.' . $storageField->name;
            }
        }

        return $result;
    }
}
