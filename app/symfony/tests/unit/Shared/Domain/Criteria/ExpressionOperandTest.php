<?php
declare(strict_types=1);

namespace unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Exception\LogicException;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;
use Throwable;

class ExpressionOperandTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testValidOperatorAndValue()
    {
        $operators = [
            ExpressionOperand::OPERATOR_EQ => 0,
            ExpressionOperand::OPERATOR_NEQ => 0,
            ExpressionOperand::OPERATOR_GT => 0,
            ExpressionOperand::OPERATOR_GTE => 0,
            ExpressionOperand::OPERATOR_LT => 0,
            ExpressionOperand::OPERATOR_LTE => 0,
            ExpressionOperand::OPERATOR_IN => [],
            ExpressionOperand::OPERATOR_NIN => []
        ];

        foreach ($operators as $operator => $value) {
            $property = $this->faker->regexify('.{1,20}');
            $operatorWithSpaces = $this->faker->regexify(' {1,5}') . $operator .
                $this->faker->regexify(' {1,20}');
            $operand = new ExpressionOperand($property, $operatorWithSpaces, $value);

            static::assertEquals($operand->property, $property);
            static::assertEquals($operand->operator, $operator);
            static::assertEquals($operand->value, $value);
        }
    }

    public function testInvalidValidOperator()
    {
        $operator = 'invalid operator';

        self::expectException(LogicException::class);
        self::expectExceptionMessage('Invalid criteria operator "' . mb_strtoupper($operator). '"');
        new ExpressionOperand('property', $operator, 0);
    }

    public function testInvalidValidValue()
    {
        $operators = [
            ExpressionOperand::OPERATOR_EQ => [],
            ExpressionOperand::OPERATOR_NEQ => [],
            ExpressionOperand::OPERATOR_GT => [],
            ExpressionOperand::OPERATOR_GTE => [],
            ExpressionOperand::OPERATOR_LT => [],
            ExpressionOperand::OPERATOR_LTE => [],
            ExpressionOperand::OPERATOR_IN => 0,
            ExpressionOperand::OPERATOR_NIN => 0
        ];
        $expectedExceptions = [];
        foreach ($operators as $value) {
            $expectedExceptions[] = [
                LogicException::class, 'Invalid criteria value type "' . gettype($value) . '"'
            ];
        }


        $actualExceptions = [];
        foreach ($operators as $operator => $value) {
            try {
                new ExpressionOperand('property', $operator, $value);
            } catch (Throwable $expected) {
                $actualExceptions[] = [
                    $expected::class, $expected->getMessage()
                ];
            }
        }

        self::assertEquals($expectedExceptions, $actualExceptions);
    }
}

