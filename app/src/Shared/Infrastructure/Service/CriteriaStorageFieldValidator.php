<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\CriteriaFilterNotExistException;
use App\Shared\Domain\Exception\CriteriaOrderNotExistException;
use App\Shared\Infrastructure\Persistence\Hydrator\Metadata\StorageMetadataInterface;

final class CriteriaStorageFieldValidator
{
    public function validate(Criteria $criteria, StorageMetadataInterface $metadata): void
    {
        $columns = $metadata->getPropertyToColumnMap();

        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as [$operator, $operand]) {
            if (!array_key_exists($operand->property, $columns)) {
                throw new CriteriaFilterNotExistException($operand->property);
            }
        }

        foreach ($criteria->getOrders() as $order) {
            if (!array_key_exists($order->property, $columns)) {
                throw new CriteriaOrderNotExistException($order->property);
            }
        }
    }
}
