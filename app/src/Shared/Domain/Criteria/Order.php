<?php
declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

final class Order
{
    public function __construct(
        public readonly string $field,
        public readonly bool $isAsc = true
    ){
    }
}
