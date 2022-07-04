<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Repository\EventRepositoryInterface;

final class SqlLogEventBusWrapper implements EventBusInterface
{
    public function __construct(
        private readonly EventBusInterface $realBus,
        private readonly EventRepositoryInterface $repository,
    ) {
    }

    public function dispatch(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            $this->repository->save($event);
        }
        $this->realBus->dispatch(...$events);
    }
}
