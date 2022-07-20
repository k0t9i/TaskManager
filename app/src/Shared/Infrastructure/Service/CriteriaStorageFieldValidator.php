<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Service;

use App\Shared\Application\Hydrator\Metadata\StorageMetadataInterface;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\CriteriaFilterNotExistException;
use App\Shared\Domain\Exception\CriteriaOrderNotExistException;

final class CriteriaStorageFieldValidator
{
    public function validate(Criteria $criteria, StorageMetadataInterface $metadata): void
    {
        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as [$operator, $operand]) {
            if (!$this->validateChildren($operand->property, $metadata)) {
                throw new CriteriaFilterNotExistException($operand->property);
            }
        }

        foreach ($criteria->getOrders() as $order) {
            if (!$this->validateChildren($operand->property, $metadata)) {
                throw new CriteriaOrderNotExistException($order->property);
            }
        }
    }

    private function validateChildren(string $property, StorageMetadataInterface $metadata): bool
    {
        $columns = $metadata->getPropertyToColumnMap();
        $parts = explode('.', $property);
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            if (!array_key_exists($part, $columns)) {
                return false;
            }
            // the last iteration is without metadata
            if ($i === count($parts) - 1) {
                break;
            }
            if ($columns[$part]->metadata === null) {
                return false;
            }
            $columns = $columns[$part]->metadata->getPropertyToColumnMap();
        }
        return true;
    }
}
