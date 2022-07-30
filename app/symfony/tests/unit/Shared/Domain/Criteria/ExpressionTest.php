<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Expression;
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

    public function testCreate(): void
    {
        $emptyExpression = new Expression();
        [$operand] = Helper::getRandomOperand();
        $expression = new Expression($operand);
        $result = [[Expression::OPERATOR_AND, $operand]];

        self::assertEquals([], $emptyExpression->getOperands());
        self::assertEquals($result, $expression->getOperands());
    }

    public function testAddOperands(): void
    {
        $expression = new Expression();

        $expected = [];
        for ($i = 0; $i < 10; ++$i) {
            $operator = $this->faker->randomElement([Expression::OPERATOR_OR, Expression::OPERATOR_AND]);
            [$operand] = Helper::getRandomOperand();
            $expected[] = [$operator, $operand];
            if (Expression::OPERATOR_AND === $operator) {
                $expression->andOperand($operand);
            } else {
                $expression->orOperand($operand);
            }
        }

        self::assertEquals($expected, $expression->getOperands());
    }
}

