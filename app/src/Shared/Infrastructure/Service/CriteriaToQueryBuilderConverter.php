<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\DTO\CriteriaJoinDTO;
use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Application\Service\CriteriaStorageFieldParserInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Expression;
use App\Shared\Domain\Criteria\ExpressionOperand;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

final class CriteriaToQueryBuilderConverter
{
    public function __construct(private readonly CriteriaStorageFieldParserInterface $parser)
    {
    }

    public function convert(QueryBuilder $queryBuilder, Criteria $criteria, StorageMetadataInterface $metadata)
    {
        $joins = $this->parser->parseJoins($criteria, $metadata);
        $columns = $this->parser->parseColumns($joins, $criteria, $metadata);

        $this->buildJoins($queryBuilder, $joins);
        $this->buildConditions($queryBuilder, $criteria, $columns);
        $this->buildOrders($queryBuilder, $criteria, $columns);

        $queryBuilder->setFirstResult($criteria->getOffset() ?? 0);
        $queryBuilder->setMaxResults($criteria->getLimit());
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
