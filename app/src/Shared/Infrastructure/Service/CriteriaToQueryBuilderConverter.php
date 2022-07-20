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
        $propertyToColumnMap = $metadata->getPropertyToColumnMap();

        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as $key => [$type, $operand]) {
            $fullName = $propertyToColumnMap[$operand->property];

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

        foreach ($criteria->getOrders() as $order) {
            $queryBuilder->addOrderBy($propertyToColumnMap[$order->property], $order->isAsc ? 'ASC' : 'DESC');
        }

        $queryBuilder->setFirstResult($criteria->getOffset() ?? 0);
        $queryBuilder->setMaxResults($criteria->getLimit());
    }
}