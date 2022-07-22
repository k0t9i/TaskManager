<?php
declare(strict_types=1);

namespace App\Shared\Domain\Service;

final class Utils
{
    public static function toSnakeCase(string $str): string
    {
        if (mb_strtolower($str) === $str) {
            return $str;
        }

        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }
}
