<?php
declare(strict_types=1);

namespace App\Shared\Application\Bus\Event;

interface EventBusInterface
{
    public function dispatch(DomainEvent ...$events): void;
}