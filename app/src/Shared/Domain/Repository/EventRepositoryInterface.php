<?php
declare(strict_types=1);

namespace App\Shared\Domain\Repository;

use App\Shared\Domain\Event\DomainEvent;

interface EventRepositoryInterface
{
    public function save(DomainEvent $event): void;
}
