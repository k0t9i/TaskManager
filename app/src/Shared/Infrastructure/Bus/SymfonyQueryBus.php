<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus;

use App\Shared\Domain\Bus\Query\QueryBusInterface;
use App\Shared\Domain\Bus\Query\QueryInterface;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class SymfonyQueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->messageBus = $queryBus;
    }

    public function dispatch(QueryInterface $query): QueryResponseInterface
    {
        return $this->handle($query);
    }
}