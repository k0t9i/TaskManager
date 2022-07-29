<?php
declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\Exception\LogicException;

final class ExpressionOperand
{
    public readonly string $operator;

    public const OPERATOR_EQ = '=';
    public const OPERATOR_NEQ = '<>';
    public const OPERATOR_GT = '>';
    public const OPERATOR_GTE = '>=';
    public const OPERATOR_LT = '<';
    public const OPERATOR_LTE = '<=';
    public const OPERATOR_IN = 'IN';
    public const OPERATOR_NIN = 'NIN';

    private static array $operators = [
        self::OPERATOR_EQ,
        self::OPERATOR_NEQ,
        self::OPERATOR_GT,
        self::OPERATOR_GTE,
        self::OPERATOR_LT,
        self::OPERATOR_LTE,
        self::OPERATOR_IN,
        self::OPERATOR_NIN
    ];

    public function __construct(
        public readonly string $property,
        string $operator,
        public readonly mixed $value
    ){
        $operator = trim($operator);
        $operator = mb_strtoupper($operator);
        $this->ensureIsValidOperator($operator);
        $this->operator = $operator;
        $this->ensureIsValidValueType($this->operator, $this->value);
    }

    private function ensureIsValidOperator(string $operator): void
    {
        if (!in_array($operator, self::$operators)) {
            throw new LogicException(sprintf('Invalid criteria operator "%s"', $operator));
        }
    }

    private function ensureIsValidValueType(string $operator, mixed $value): void
    {
        $isArrayOperator = in_array($operator, [self::OPERATOR_IN, self::OPERATOR_NIN]);
        if ($isArrayOperator && !is_array($value) || !$isArrayOperator && is_array($value)) {
            throw new LogicException(sprintf('Invalid criteria value type "%s"', gettype($value)));
        }
    }
}
