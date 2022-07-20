<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Expression;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class CriteriaToQueryBuilderConverter
{
    public function convert(QueryBuilder $queryBuilder, Criteria $criteria, StorageMetadataInterface $metadata)
    {
        $joins = $this->prepareJoins($criteria, $metadata);
        $columns = $this->prepareColumns($joins, $criteria, $metadata);

        $this->buildJoins($queryBuilder, $joins);
        $this->buildConditions($queryBuilder, $criteria, $columns);
        $this->buildOrders($queryBuilder, $criteria, $columns);

        $queryBuilder->setFirstResult($criteria->getOffset() ?? 0);
        $queryBuilder->setMaxResults($criteria->getLimit());
    }

    private function buildJoins(QueryBuilder $queryBuilder, array $joins): void
    {
        foreach ($joins as $join) {
            $queryBuilder->leftJoin(...$join);
        }
    }

    private function buildConditions(QueryBuilder $queryBuilder, Criteria $criteria, array $columns): void
    {
        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as $key => [$type, $operand]) {
            $fullName = $columns[$operand->property];

            $parameter = 'param' . $key;
            $queryParameter = ':' . $parameter;
            if (is_array($operand->value)) {
                $queryParameter = '(' . $queryParameter . ')';
            }

            $where = $fullName . ' ' . $operand->operator . ' ' . $queryParameter;

            if ($type === Expression::OPERATOR_AND) {
                $queryBuilder->andWhere($where);
            } else {
                $queryBuilder->orWhere($where);
            }

            $queryBuilder->setParameter(
                $parameter,
                $operand->value,
                is_array($operand->value) ? Connection::PARAM_STR_ARRAY : null
            );
        }
    }

    private function buildOrders(QueryBuilder $queryBuilder, Criteria $criteria, array $columns): void
    {
        foreach ($criteria->getOrders() as $order) {
            $queryBuilder->addOrderBy(
                $columns[$order->property],
                $order->isAsc ? 'ASC' : 'DESC'
            );
        }
    }

    private function prepareJoins(Criteria $criteria, StorageMetadataInterface $metadata): array
    {
        $result = [];
        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as [$type, $operand]) {
            $result = array_merge($result, $this->preparePartOfJoins($operand->property, $metadata));
        }
        foreach ($criteria->getOrders() as $order) {
            $result = array_merge($result, $this->preparePartOfJoins($order->property, $metadata));
        }
        return $result;
    }

    private function prepareColumns(array $joins, Criteria $criteria, StorageMetadataInterface $metadata): array
    {
        $result = [];

        if (count($joins) > 0) {
            foreach ($criteria->getExpression()->getOperands() as [$type, $operand]) {
                $result = array_merge($result, $this->preparePartOfColumns($operand->property, $metadata));
            }
            foreach ($criteria->getOrders() as $order) {
                $result = array_merge($result, $this->preparePartOfColumns($order->property, $metadata));
            }
        } else {
            foreach ($metadata->getPropertyToColumnMap() as $property => $metadataField) {
                $result[$property] = $metadataField->name;
            }
        }

        return $result;
    }

    private function preparePartOfJoins(string $property, StorageMetadataInterface $metadata): array
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
                $result[$joinTable] = [
                    $parentTable,
                    $joinTable,
                    $joinTable,
                    implode('AND', $condition)
                ];
                $parentTable = $storageField->metadata->getStorageName();
                $columns = $storageField->metadata->getPropertyToColumnMap();
            }
        }

        return $result;
    }

    private function preparePartOfColumns(string $property, StorageMetadataInterface $metadata): array
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
