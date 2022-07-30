<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\Service;

use App\Shared\Domain\Service\Utils;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    public function testToSnakeCase(): void
    {
        $items = [
            'CamelCaseFourWords' => 'camel_case_four_words',
            'CamelCase5Words' => 'camel_case5_words',
            'pascalCaseFourWords' => 'pascal_case_four_words',
            'lowercase' => 'lowercase',
            'UPPERCASE' => 'u_p_p_e_r_c_a_s_e',
            'snake_case_whatever' => 'snake_case_whatever',
            'A' => 'a',
            '_Ca_meCa_seWi_thUnder_line' => '__ca_me_ca_se_wi_th_under_line',
            '1234567890' => '1234567890',
        ];

        foreach ($items as $source => $target) {
            self::assertEquals($target, Utils::toSnakeCase((string) $source));
        }
    }
}
