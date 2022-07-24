<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Expression;
use App\Shared\Domain\Criteria\ExpressionOperand;
use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use Doctrine\Common\Collections\Expr\Comparison;

final class CriteriaToDoctrineCriteriaConverter implements CriteriaToDoctrineCriteriaConverterInterface
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
    }
}
