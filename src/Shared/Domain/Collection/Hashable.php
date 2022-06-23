<?php
declare(strict_types=1);

namespace App\Shared\Domain\Collection;

interface Hashable
{
    public function getHash(): string;
}
