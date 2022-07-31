<?php

declare(strict_types=1);

namespace App\Shared\Application\Bus\Event;

interface EventSubscriberInterface
{
    public function subscribeTo(): array;
}
