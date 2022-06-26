<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectMemberships\Domain\Entity;

use App\ProjectMemberships\Domain\Entity\Membership;
use App\ProjectMemberships\Domain\Event\ProjectOwnerWasChangedEvent;
use App\ProjectMemberships\Domain\Event\ProjectParticipantWasRemovedEvent;
use App\ProjectMemberships\Domain\Exception\InsufficientPermissionsToChangeProjectMembershipParticipantException;
use App\ProjectMemberships\Domain\Exception\ProjectMembershipParticipantNotExistException;
use App\ProjectMemberships\Domain\Exception\UserHasProjectMembershipTaskException;
use App\Shared\Domain\Exception\ModificationDeniedException;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsAlreadyParticipantException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\UserId;
use PHPUnit\Framework\TestCase;

class MembershipTest extends TestCase
{
    private MembershipMother $mother;

    protected function setUp(): void
    {
        $this->mother = new MembershipMother();
    }

    public function testParticipantCanRemoveItself(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->byParticipantItself();

        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));

        $this->removeParticipantPositiveAssertions($membership, $participantId);
    }

    public function testOwnerCanRemoveOtherParticipant(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantByOwner();

        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));

        $this->removeParticipantPositiveAssertions($membership, $participantId);
    }

    public function testRemoveParticipantInClosedMembership(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantInClosedMembership();

        self::expectException(ModificationDeniedException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testRemoveParticipantByNonOwnerAndNonParticipant(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantByNonOwnerAndNonParticipant();

        self::expectException(InsufficientPermissionsToChangeProjectMembershipParticipantException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testRemoveNonExistingParticipant(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantNonExistingParticipant();

        self::expectException(ProjectMembershipParticipantNotExistException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testRemoveParticipantWithTask(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantWithTask();

        self::expectException(UserHasProjectMembershipTaskException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testChangeOwner(): void
    {
        /** @var Membership $membership */
        [$ownerId, $currentUserId, $membership] = $this->mother->changeOwnerByOwner();

        $membership->changeOwner(new UserId($ownerId), new UserId($currentUserId));

        self::assertEquals($ownerId, $membership->getOwnerId()->value);
        $events = $membership->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        /** @var ProjectOwnerWasChangedEvent $event */
        $event = $events[0];
        self::assertInstanceOf(ProjectOwnerWasChangedEvent::class, $event);
        self::assertEquals($membership->getId()->value, $event->aggregateId);
        self::assertEquals($ownerId, $event->ownerId);
    }

    public function testChangeOwnerInClosedMembership(): void
    {
        [$ownerId, $currentUserId, $membership] = $this->mother->changeOwnerInClosedMembership();

        self::expectException(ModificationDeniedException::class);
        $membership->removeParticipant(new UserId($ownerId), new UserId($currentUserId));
    }

    public function testChangeOwnerByParticipant(): void
    {
        [$ownerId, $currentUserId, $membership] = $this->mother->changeOwnerByParticipant();

        self::expectException(UserIsNotOwnerException::class);
        $membership->changeOwner(new UserId($ownerId), new UserId($currentUserId));
    }

    public function testChangeOwnerToSame(): void
    {
        [$ownerId, $currentUserId, $membership] = $this->mother->changeOwnerToSame();

        self::expectException(UserIsAlreadyOwnerException::class);
        $membership->changeOwner(new UserId($ownerId), new UserId($currentUserId));
    }

    public function testChangeOwnerWithTask(): void
    {
        [$ownerId, $currentUserId, $membership] = $this->mother->changeOwnerWithTask();

        self::expectException(UserHasProjectMembershipTaskException::class);
        $membership->changeOwner(new UserId($ownerId), new UserId($currentUserId));
    }

    public function testChangeOwnerToExistingParticipant(): void
    {
        [$ownerId, $currentUserId, $membership] = $this->mother->changeOwnerExistingParticipant();

        self::expectException(UserIsAlreadyParticipantException::class);
        $membership->changeOwner(new UserId($ownerId), new UserId($currentUserId));
    }

    private function removeParticipantPositiveAssertions(
        Membership $membership,
        string $participantId
    ): void {
        self::assertCount(0, $membership->getParticipantIds());
        $events = $membership->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        /** @var ProjectParticipantWasRemovedEvent $event */
        $event = $events[0];
        self::assertInstanceOf(ProjectParticipantWasRemovedEvent::class, $event);
        self::assertEquals($membership->getId()->value, $event->aggregateId);
        self::assertEquals($participantId, $event->participantId);
    }
}

