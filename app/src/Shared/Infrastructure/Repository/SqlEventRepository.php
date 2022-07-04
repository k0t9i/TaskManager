<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Repository\EventRepositoryInterface;
use App\Shared\Domain\Service\UuidGeneratorInterface;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

final class SqlEventRepository implements EventRepositoryInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UuidGeneratorInterface $uuidGenerator,
    ) {
    }

    /**
     * @param DomainEvent $event
     * @throws Exception
     */
    public function save(DomainEvent $event): void
    {
        $this->entityManager->getConnection()->createQueryBuilder()
            ->insert('events')
            ->values([
                'id' => '?',
                'aggregate_id' => '?',
                'name' => '?',
                'body' => '?',
                'occurred_on' => '?'
            ])
            ->setParameters([
                $this->uuidGenerator->generate(),
                $event->aggregateId,
                $event::getEventName(),
                json_encode($event->toPrimitives()),
                $event->occurredOn
            ])
            ->executeStatement();
    }
}
