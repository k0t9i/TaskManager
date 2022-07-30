<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Expression;
use App\Shared\Domain\Criteria\ExpressionOperand;
use App\Shared\Domain\Criteria\Order;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class CriteriaTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }

    public function testCreate(): void
    {
        /** @var Criteria $criteria */
        [$criteria, $expression, $orders, $offset, $limit] = $this->getRandomCriteria();
        $emptyCriteria = new Criteria();
        $emptyExpression = new Expression();

        self::assertEquals($expression, $criteria->getExpression());
        self::assertEquals($orders, $criteria->getOrders());
        self::assertEquals($offset, $criteria->getOffset());
        self::assertEquals($limit, $criteria->getLimit());
        self::assertEquals($emptyExpression, $emptyCriteria->getExpression());
        self::assertEquals([], $emptyCriteria->getOrders());
        self::assertEquals(null, $emptyCriteria->getOffset());
        self::assertEquals(null, $emptyCriteria->getLimit());
    }

    public function testReset(): void
    {
        /** @var Criteria $criteria */
        [$criteria] = $this->getRandomCriteria();
        $emptyExpression = new Expression();

        $criteria->reset();
        self::assertEquals($emptyExpression, $criteria->getExpression());
        self::assertEquals([], $criteria->getOrders());
        self::assertEquals(null, $criteria->getOffset());
        self::assertEquals(null, $criteria->getLimit());
    }

    public function testLoadScalarFilters(): void
    {
        /** @var Criteria $criteria */
        /** @var Expression $expression */
        [$criteria, $expression, $orders, $offset, $limit] = $this->getRandomCriteria();

        $scalarFilters = [];
        $arrayValue = $this->faker->words(10);
        for ($i = 0; $i < 10; ++$i) {
            $isArray = 1 === mt_rand(0, 1);
            $value = $isArray ? $this->faker->shuffleArray($arrayValue) : $this->faker->regexify('.{1,20}');
            $scalarFilters[$this->faker->regexify('.{1,20}')] = $value;
        }

        foreach ($scalarFilters as $name => $value) {
            $operator = ExpressionOperand::OPERATOR_EQ;
            if (is_array($value)) {
                $operator = ExpressionOperand::OPERATOR_IN;
            }
            $expression->andOperand(new ExpressionOperand((string) $name, $operator, $value));
        }

        $criteria->loadScalarFilters($scalarFilters);

        self::assertEquals($expression, $criteria->getExpression());
        self::assertEquals($orders, $criteria->getOrders());
        self::assertEquals($offset, $criteria->getOffset());
        self::assertEquals($limit, $criteria->getLimit());
    }

    public function testLoadScalarOrders(): void
    {
        /** @var Criteria $criteria */
        /** @var Expression $expression */
        [$criteria, $expression, $orders, $offset, $limit] = $this->getRandomCriteria();

        $scalarOrders = [];
        for ($i = 0; $i < 10; ++$i) {
            $scalarOrders[$this->faker->regexify('.{1,20}')] = 1 === mt_rand(0, 1);
        }

        foreach ($scalarOrders as $name => $isAsc) {
            $orders[] = new Order((string) $name, $isAsc);
        }

        $criteria->loadScalarOrders($scalarOrders);

        self::assertEquals($expression, $criteria->getExpression());
        self::assertEquals($orders, $criteria->getOrders());
        self::assertEquals($offset, $criteria->getOffset());
        self::assertEquals($limit, $criteria->getLimit());
    }

    public function testLoadOffsetAndLimit(): void
    {
        /** @var Criteria $criteria */
        [$criteria, $expression, $orders] = $this->getRandomCriteria();

        $offset = mt_rand(0, 10);
        $limit = mt_rand(0, 10);

        $criteria->loadOffsetAndLimit($offset, $limit);

        self::assertEquals($expression, $criteria->getExpression());
        self::assertEquals($orders, $criteria->getOrders());
        self::assertEquals($offset, $criteria->getOffset());
        self::assertEquals($limit, $criteria->getLimit());
    }

    private function getRandomCriteria(): array
    {
        $filters = [];
        $expression = new Expression();
        for ($i = 0; $i < 10; ++$i) {
            [$operand] = Helper::getRandomOperand();
            $filters[] = $operand;
            $expression->andOperand($operand);
        }

        $orders = [];
        for ($i = 0; $i < 10; ++$i) {
            $order = new Order(
                $this->faker->regexify('.{1,20}'),
                1 === mt_rand(0, 1)
            );
            $orders[] = $order;
        }
        $offset = mt_rand(0, 10);
        $limit = mt_rand(0, 10);
        $criteria = new Criteria($filters, $orders, $offset, $limit);

        return [$criteria, $expression, $orders, $offset, $limit];
    }
}
