<?php

declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\CriteriaFilterNotExistException;
use App\Shared\Domain\Exception\CriteriaOrderNotExistException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

final class CriteriaFieldValidator implements CriteriaFieldValidatorInterface
{
    /**
     * @throws ReflectionException
     */
    public function validate(Criteria $criteria, string $class): void
    {
        $reflection = new ReflectionClass($class);
        /**
         * @var ExpressionOperand $operand
         */
        foreach ($criteria->getExpression()->getOperands() as [$operator, $operand]) {
            if (!$this->checkProperty($reflection, $operand->property)) {
                throw new CriteriaFilterNotExistException($operand->property);
            }
        }

        foreach ($criteria->getOrders() as $order) {
            if (!$this->checkProperty($reflection, $order->property)) {
                throw new CriteriaOrderNotExistException($order->property);
            }
        }
    }

    private function checkProperty(ReflectionClass $reflection, string $propertyName): bool
    {
        $property = $reflection->hasProperty($propertyName) ? $reflection->getProperty($propertyName) : null;
        if (null === $property) {
            return false;
        }

        $type = $property->getType();
        if (!($type instanceof ReflectionNamedType)) {
            return false;
        }

        // Only scalar types
        return !class_exists($type->getName());
    }
}
