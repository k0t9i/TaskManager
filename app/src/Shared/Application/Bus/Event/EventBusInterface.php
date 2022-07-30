<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\Event;

use App\Shared\Domain\Event\DomainEvent;

interface EventBusInterface
{
    public function dispatch(DomainEvent ...$events): void;
}
