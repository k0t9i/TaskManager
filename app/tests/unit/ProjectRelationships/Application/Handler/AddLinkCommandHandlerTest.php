<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRelationships\Application\Handler;

use App\ProjectRelationships\Application\Command\AddLinkCommand;
use App\ProjectRelationships\Application\Handler\AddLinkCommandHandler;
use App\ProjectRelationships\Domain\Entity\Relationship;
use App\ProjectRelationships\Domain\Exception\ProjectRelationshipNotExistException;
use App\ProjectRelationships\Domain\Repository\RelationshipRepositoryInterface;
use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class AddLinkCommandHandlerTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/ProjectRelationships/*']);
        BypassFinals::enable();
        $this->faker = Factory::create();
    }

    public function testPositive()
    {
        $fromTaskId = $this->faker->uuid();
        $toTaskId = $this->faker->uuid();
        $userId = $this->faker->uuid();

        $relationship = $this->getMockBuilder(Relationship::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createLink', 'releaseEvents'])
            ->getMock();
        $relationship->expects(self::once())
            ->method('createLink')
            ->with(
                self::equalTo(new RelationshipTaskId($fromTaskId)),
                self::equalTo(new RelationshipTaskId($toTaskId)),
                self::equalTo(new UserId($userId))
            );
        $releaseEventsResult = [
            $this->getMockForAbstractClass(DomainEvent::class, callOriginalConstructor: false)
        ];
        $relationship->expects(self::once())
            ->method('releaseEvents')
            ->willReturn($releaseEventsResult);

        $relationshipRepository = $this->getMockForAbstractClass(
            RelationshipRepositoryInterface::class,
            mockedMethods: ['findByTaskId', 'update']
        );
        $relationshipRepository->expects(self::once())
            ->method('findByTaskId')
            ->with(new RelationshipTaskId($fromTaskId))
            ->willReturn($relationship);
        $relationshipRepository->expects(self::once())
            ->method('update')
            ->with($relationship);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class,
            mockedMethods: ['dispatch']
        );
        $eventBus->expects(self::once())
            ->method('dispatch')
            ->with(...$releaseEventsResult);

        $handler = new AddLinkCommandHandler(
            $relationshipRepository,
            $eventBus
        );
        $command = new AddLinkCommand($fromTaskId, $toTaskId, $userId);

        $handler->__invoke($command);
    }

    public function testNonExistingRelationship(): void
    {
        $fromTaskId = $this->faker->uuid();
        $toTaskId = $this->faker->uuid();
        $userId = $this->faker->uuid();

        $relationshipRepository = $this->getMockForAbstractClass(
            RelationshipRepositoryInterface::class,
            mockedMethods: ['findByTaskId']
        );
        $relationshipRepository->expects(self::once())
            ->method('findByTaskId')
            ->with(new RelationshipTaskId($fromTaskId))
            ->willReturn(null);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class,
        );

        $handler = new AddLinkCommandHandler(
            $relationshipRepository,
            $eventBus
        );
        $command = new AddLinkCommand($fromTaskId, $toTaskId, $userId);

        self::expectException(ProjectRelationshipNotExistException::class);
        $handler->__invoke($command);
    }
}
