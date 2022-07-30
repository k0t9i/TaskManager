<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\ExpressionOperand;
use Faker\Factory;
use Faker\Generator;

final class Helper
{
    private static ?Generator $faker = null;

    public static function getRandomOperand(?string $operator = null): array
    {
        if (null === self::$faker) {
            self::$faker = Factory::create();
        }

        $arrayValue = self::$faker->words(10);
        $operators = [
            ExpressionOperand::OPERATOR_EQ => self::$faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_NEQ => self::$faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_GT => self::$faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_GTE => self::$faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_LT => self::$faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_LTE => self::$faker->randomElement($arrayValue),
            ExpressionOperand::OPERATOR_IN => self::$faker->shuffleArray($arrayValue),
            ExpressionOperand::OPERATOR_NIN => self::$faker->shuffleArray($arrayValue),
        ];
        $operator = $operator ?? self::$faker->randomKey($operators);
        $value = $operators[$operator];
        $property = self::$faker->regexify('.{1,20}');
        $operand = new ExpressionOperand(
            $property,
            $operator,
            $value
        );

        return [$operand, $property, $operator, $value];
    }
}
