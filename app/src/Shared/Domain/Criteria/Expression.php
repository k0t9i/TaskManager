<?php
declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

final class Expression
{
    public const OPERATOR_AND = 'AND';
    public const OPERATOR_OR = 'OR';

    private array $operands = [];

    public function __construct(?ExpressionOperand $operand = null)
    {
        if ($operand !== null) {
            $this->andOperand($operand);
        }
    }

    public function andOperand(ExpressionOperand $operand): self
    {
        $this->operands[] = [
            self::OPERATOR_AND,
            $operand
        ];
        return $this;
    }

    public function orOperand(ExpressionOperand $operand): self
    {
        $this->operands[] = [
            self::OPERATOR_OR,
            $operand
        ];
        return $this;
    }

    public function getOperands(): array
    {
        return $this->operands;
    }
}
