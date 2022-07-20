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
        $columns = [];
        foreach ($metadata->getStorageFields() as $metadataField) {
            $columns[$metadataField->name] = $metadataField->name;
        }

        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as [$operator, $operand]) {
            if (!array_key_exists($operand->field, $columns)) {
                throw new CriteriaFilterNotExistException($operand->field);
            }
        }

        foreach ($criteria->getOrders() as $order) {
            if (!array_key_exists($order->field, $columns)) {
                throw new CriteriaOrderNotExistException($order->field);
            }
        }
    }
}
