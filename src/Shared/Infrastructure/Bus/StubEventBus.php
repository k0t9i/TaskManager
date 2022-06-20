<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;

class StubEventBus implements EventBusInterface
{
    public function dispatch(DomainEvent ...$event): void
    {
        // TODO: Implement dispatch() method.
    }
}