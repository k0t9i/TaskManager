<?php
declare(strict_types=1);

namespace unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Expression;
use App\Shared\Domain\Criteria\ExpressionOperand;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class ExpressionTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testCreate()
    {
        $emptyExpression = new Expression();
        $operand = $this->getRandomOperand();
        $expression = new Expression($operand);
        $result = [[Expression::OPERATOR_AND, $operand]];

        self::assertEquals([], $emptyExpression->getOperands());
        self::assertEquals($result, $expression->getOperands());
    }

    public function testAddOperands()
    {
        $expression = new Expression();

        $expected = [];
        for ($i = 0; $i < mt_rand(1, 10); $i++) {
            $operator = $this->faker->randomElement([Expression::OPERATOR_OR, Expression::OPERATOR_AND]);
            $operand = $this->getRandomOperand();
            $expected[] = [$operator, $operand];
            if ($operator === Expression::OPERATOR_AND) {
                $expression->andOperand($operand);
            } else {
                $expression->orOperand($operand);
            }
        }

        self::assertEquals($expected, $expression->getOperands());
    }

    private function getRandomOperand(): ExpressionOperand
    {
        $arrayValue = $this->faker->words(10);
        $operators = [
            ExpressionOperand::OPERATOR_EQ => $this->faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_NEQ => $this->faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_GT => $this->faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_GTE => $this->faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_LT => $this->faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_LTE => $this->faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_IN => $this->faker->shuffleArray($arrayValue),
            ExpressionOperand::OPERATOR_NIN => $this->faker->shuffleArray($arrayValue)
        ];
        $operator = $this->faker->randomKey($operators);
        $value = $operators[$operator];

        return new ExpressionOperand(
            $this->faker->regexify('.{1,20}'),
            $operator,
            $value
        );
    }
}

