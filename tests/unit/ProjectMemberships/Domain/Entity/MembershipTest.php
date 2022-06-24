<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectMemberships\Domain\Entity;

use App\ProjectMemberships\Domain\Entity\Membership;
use App\ProjectMemberships\Domain\Exception\InsufficientPermissionsToChangeProjectParticipantException;
use App\ProjectMemberships\Domain\Exception\ProjectOwnerOwnsProjectTaskException;
use App\ProjectMemberships\Domain\Exception\ProjectParticipantNotExistException;
use App\ProjectMemberships\Domain\Exception\UserHasProjectTaskException;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Exception\ModificationDeniedException;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\UserId;
use DG\BypassFinals;
use PHPUnit\Framework\TestCase;

class MembershipTest extends TestCase
{
    private MembershipMother $mother;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/*']);
        BypassFinals::enable();
        $this->mother = new MembershipMother();
    }

    public function testParticipantCanRemoveItself(): void
    {
        [$participantId, $currentUserId, $expectedEvent, $membership] = $this->mother->byParticipantItself();

        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));

        $this->removeParticipantPositiveAssertions($membership, $expectedEvent);
    }

    public function testOwnerCanRemoveOtherParticipant(): void
    {
        [$participantId, $currentUserId, $expectedEvent, $membership] = $this->mother->removeParticipantByOwner();

        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));

        $this->removeParticipantPositiveAssertions($membership, $expectedEvent);
    }

    public function testRemoveParticipantInCloseProject(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantInCloseProject();

        self::expectException(ModificationDeniedException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testRemoveParticipantByNonOwnerAndNonParticipant(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantByNonOwnerAndNonParticipant();

        self::expectException(InsufficientPermissionsToChangeProjectParticipantException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testRemoveNonExistingParticipant(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantNonExistingParticipant();

        self::expectException(ProjectParticipantNotExistException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testRemoveParticipantWithTask(): void
    {
        [$participantId, $currentUserId, $membership] = $this->mother->removeParticipantWithTask();

        self::expectException(UserHasProjectTaskException::class);
        $membership->removeParticipant(new UserId($participantId), new UserId($currentUserId));
    }

    public function testChangeOwner(): void
    {
        [$ownerId, $currentUserId, $expectedEvent, $membership] = $this->mother->changeOwnerByOwner();

        $membership->changeOwner(new UserId($ownerId), new UserId($currentUserId));

        self::assertEquals($ownerId, $membership->getOwnerId()->value);
        $events = $membership->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        $event = $events[0];
        self::assertEquals($expectedEvent, $event);
    }

    public function testChangeOwnerInCloseProject(): void
    {
        [$ownerId, $currentUserId, $membership] = $this->mother->changeOwnerInCloseProject();

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

        self::expectException(ProjectOwnerOwnsProjectTaskException::class);
        $membership->changeOwner(new UserId($ownerId), new UserId($currentUserId));
    }

    private function removeParticipantPositiveAssertions(Membership $membership, DomainEvent $expectedEvent): void
    {
        self::assertCount(0, $membership->getParticipantIds());
        $events = $membership->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        $event = $events[0];
        self::assertEquals($expectedEvent, $event);
    }
}
