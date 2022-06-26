<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRelationships\Domain\Entity;

use App\ProjectRelationships\Domain\Entity\Relationship;
use App\ProjectRelationships\Domain\Event\TaskLinkWasAddedEvent;
use App\ProjectRelationships\Domain\Event\TaskLinkWasDeletedEvent;
use App\ProjectRelationships\Domain\Exception\ProjectRelationshipTaskLinkAlreadyExistsException;
use App\ProjectRelationships\Domain\Exception\ProjectRelationshipTaskLinkNotExistException;
use App\ProjectRelationships\Domain\Exception\ProjectRelationshipTaskNotExistException;
use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;
use App\ProjectRelationships\Domain\ValueObject\TaskLink;
use App\Shared\Domain\Exception\ModificationDeniedException;
use App\Shared\Domain\ValueObject\UserId;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class RelationshipTest extends TestCase
{
    private RelationshipMother $mother;
    private Generator $faker;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/*']);
        BypassFinals::enable();
        $this->mother = new RelationshipMother();
        $this->faker = Factory::create();
    }

    public function testCreateLinkByOwner(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId2, $currentUserId, $relationship] = $this->mother->createLinkByOwner();

        $relationship->createLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId2),
            new UserId($currentUserId)
        );

        $this->createLinkPositiveAssertions($relationship, $taskId1, $taskId2);
    }

    public function testCreateLinkByTaskOwner(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId2, $currentUserId, $relationship] = $this->mother->createLinkByTaskOwner();

        $relationship->createLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId2),
            new UserId($currentUserId)
        );

        $this->createLinkPositiveAssertions($relationship, $taskId1, $taskId2);
    }

    public function testCreateLinkInClosedRelationship(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId2, $currentUserId, $relationship] = $this->mother->createLinkInClosedRelationship();

        self::expectException(ModificationDeniedException::class);
        $relationship->createLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId2),
            new UserId($currentUserId)
        );
    }

    public function testCreateLinkForNonExistingFromTask(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId2, $currentUserId, $relationship] = $this->mother->createLinkByOwner();

        self::expectException(ProjectRelationshipTaskNotExistException::class);
        $relationship->createLink(
            new RelationshipTaskId($this->faker->uuid()),
            new RelationshipTaskId($taskId2),
            new UserId($currentUserId)
        );
    }

    public function testCreateLinkForNonExistingToTask(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId2, $currentUserId, $relationship] = $this->mother->createLinkByOwner();

        self::expectException(ProjectRelationshipTaskNotExistException::class);
        $relationship->createLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($this->faker->uuid()),
            new UserId($currentUserId)
        );
    }

    public function testCreateExistingLink(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId3, $currentUserId, $relationship] = $this->mother->createExistingLink();

        self::expectException(ProjectRelationshipTaskLinkAlreadyExistsException::class);
        $relationship->createLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId3),
            new UserId($currentUserId)
        );
    }

    public function testCreateReverseExistingLink(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId3, $currentUserId, $relationship] = $this->mother->createExistingLink();

        self::expectException(ProjectRelationshipTaskLinkAlreadyExistsException::class);
        $relationship->createLink(
            new RelationshipTaskId($taskId3),
            new RelationshipTaskId($taskId1),
            new UserId($currentUserId)
        );
    }

    public function testDeleteLinkByOwner(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId3, $currentUserId, $relationship] = $this->mother->deleteLinkByOwner();

        $relationship->deleteLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId3),
            new UserId($currentUserId)
        );

        $this->deleteLinkPositiveAssertions($relationship, $taskId1, $taskId3);
    }

    public function testDeleteLinkByTaskOwner(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId3, $currentUserId, $relationship] = $this->mother->deleteLinkByTaskOwner();

        $relationship->deleteLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId3),
            new UserId($currentUserId)
        );

        $this->deleteLinkPositiveAssertions($relationship, $taskId1, $taskId3);
    }

    public function testDeleteLinkInClosedRelationship(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId3, $currentUserId, $relationship] = $this->mother->deleteLinkInClosedRelationship();

        self::expectException(ModificationDeniedException::class);
        $relationship->deleteLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId3),
            new UserId($currentUserId)
        );
    }

    public function testDeleteLinkForNonExistingFromTask(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId3, $currentUserId, $relationship] = $this->mother->deleteLinkByOwner();

        self::expectException(ProjectRelationshipTaskNotExistException::class);
        $relationship->deleteLink(
            new RelationshipTaskId($this->faker->uuid()),
            new RelationshipTaskId($taskId3),
            new UserId($currentUserId)
        );
    }

    public function testDeleteLinkForNonExistingToTask(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId3, $currentUserId, $relationship] = $this->mother->deleteLinkByOwner();

        self::expectException(ProjectRelationshipTaskNotExistException::class);
        $relationship->deleteLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($this->faker->uuid()),
            new UserId($currentUserId)
        );
    }

    public function testDeleteNonExistingLink(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId2, $currentUserId, $relationship] = $this->mother->deleteNonExistingLink();

        self::expectException(ProjectRelationshipTaskLinkNotExistException::class);
        $relationship->deleteLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId2),
            new UserId($currentUserId)
        );
    }

    public function testDeleteReverseNonExistingLink(): void
    {
        /** @var Relationship $relationship */
        [$taskId1, $taskId2, $currentUserId, $relationship] = $this->mother->deleteNonExistingLink();

        self::expectException(ProjectRelationshipTaskLinkNotExistException::class);
        $relationship->deleteLink(
            new RelationshipTaskId($taskId2),
            new RelationshipTaskId($taskId1),
            new UserId($currentUserId)
        );
    }

    private function createLinkPositiveAssertions(
        Relationship $relationship,
        string $taskId1,
        string $taskId2
    ): void {
        self::assertCount(2, $relationship->getLinks());
        self::assertTrue($relationship->getLinks()->exists(new TaskLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId2),
        )));
        $events = $relationship->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        /** @var TaskLinkWasAddedEvent $event */
        $event = $events[0];
        self::assertInstanceOf(TaskLinkWasAddedEvent::class, $event);
        self::assertEquals($relationship->getId()->value, $event->aggregateId);
        self::assertEquals($taskId1, $event->fromTaskId);
        self::assertEquals($taskId2, $event->toTaskId);
    }

    private function deleteLinkPositiveAssertions(
        Relationship $relationship,
        string $taskId1,
        string $taskId3
    ): void {
        self::assertCount(0, $relationship->getLinks());
        self::assertFalse($relationship->getLinks()->exists(new TaskLink(
            new RelationshipTaskId($taskId1),
            new RelationshipTaskId($taskId3),
        )));
        $events = $relationship->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        /** @var TaskLinkWasDeletedEvent $event */
        $event = $events[0];
        self::assertInstanceOf(TaskLinkWasDeletedEvent::class, $event);
        self::assertEquals($relationship->getId()->value, $event->aggregateId);
        self::assertEquals($taskId1, $event->fromTaskId);
        self::assertEquals($taskId3, $event->toTaskId);
    }
}

