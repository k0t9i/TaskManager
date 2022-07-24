<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\DTO\CriteriaJoinDTO;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Expression;
use App\Shared\Domain\Criteria\ExpressionOperand;
use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class CriteriaToDoctrineCriteriaConverter
{
    public function convert(Criteria $criteria): DoctrineCriteria
    {
        $result = new DoctrineCriteria();

        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as [$operator, $operand]) {
            if ($operator === Expression::OPERATOR_AND) {
                $result->andWhere(new Comparison($operand->property, $operand->operator, $operand->value));
            } else {
                $result->orWhere(new Comparison($operand->property, $operand->operator, $operand->value));
            }
        }
        $orderings = [];
        foreach ($criteria->getOrders() as $order) {
            $orderings[$order->property] = $order->isAsc ? DoctrineCriteria::ASC : DoctrineCriteria::DESC;
        }
        $result->orderBy($orderings);

        $result->setFirstResult($criteria->getOffset() ?? 0);
        $result->setMaxResults($criteria->getLimit());

        return $result;
        /*$joins = $this->parser->parseJoins($criteria, $metadata);
        $columns = $this->parser->parseColumns($joins, $criteria, $metadata);

        $this->buildJoins($queryBuilder, $joins);
        $this->buildConditions($queryBuilder, $criteria, $columns);
        $this->buildOrders($queryBuilder, $criteria, $columns);

        $queryBuilder->setFirstResult($criteria->getOffset() ?? 0);
        $queryBuilder->setMaxResults($criteria->getLimit());*/
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param CriteriaJoinDTO[] $joins
     */
    private function buildJoins(QueryBuilder $queryBuilder, array $joins): void
    {
        foreach ($joins as $join) {
            $queryBuilder->leftJoin(
                $join->parentAlias,
                $join->joinTable,
                $join->joinAlias,
                $join->condition
            );
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
}
