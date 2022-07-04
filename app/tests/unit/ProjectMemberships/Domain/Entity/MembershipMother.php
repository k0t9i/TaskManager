<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectMemberships\Domain\Entity;

use App\ProjectMemberships\Domain\Entity\Membership;
use App\ProjectMemberships\Domain\ValueObject\MembershipId;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\ActiveProjectStatus;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;
use Faker\Factory;
use Faker\Generator;

final class MembershipMother
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function byParticipantItself(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $participantId;

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$participantId, $currentUserId, $membership];
    }

    public function removeParticipantByOwner(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $ownerId;

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$participantId, $currentUserId, $membership];
    }

    public function removeParticipantInClosedMembership(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $participantId;

        $membership = $this->createMembership(
            $membershipId,
            $ownerId,
            $participantId,
            $taskOwnerId,
            new ClosedProjectStatus()
        );

        return [$participantId, $currentUserId, $membership];
    }

    public function removeParticipantByNonOwnerAndNonParticipant(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $this->faker->uuid();

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$participantId, $currentUserId, $membership];
    }

    public function removeParticipantNonExistingParticipant(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $existingParticipantId = $this->faker->uuid();
        $currentUserId = $ownerId;

        $membership = $this->createMembership($membershipId, $ownerId, $existingParticipantId, $taskOwnerId);

        return [$participantId, $currentUserId, $membership];
    }

    public function removeParticipantWithTask(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $taskOwnerId = $participantId;
        $currentUserId = $ownerId;

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$participantId, $currentUserId, $membership];
    }

    public function changeOwnerByOwner(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $ownerId;
        $newOwnerId = $this->faker->uuid();

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$newOwnerId, $currentUserId, $membership];
    }

    public function changeOwnerInClosedMembership(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $participantId;
        $newOwnerId = $this->faker->uuid();

        $membership = $this->createMembership(
            $membershipId,
            $ownerId,
            $participantId,
            $taskOwnerId,
            new ClosedProjectStatus()
        );

        return [$newOwnerId, $currentUserId, $membership];
    }

    public function changeOwnerByParticipant(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $participantId;
        $newOwnerId = $this->faker->uuid();

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$newOwnerId, $currentUserId, $membership];
    }

    public function changeOwnerToSame(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $taskOwnerId = $ownerId;
        $currentUserId = $ownerId;

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$ownerId, $currentUserId, $membership];
    }

    public function changeOwnerWithTask(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $taskOwnerId = $ownerId;
        $currentUserId = $ownerId;
        $newOwnerId = $this->faker->uuid();

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$newOwnerId, $currentUserId, $membership];
    }

    public function changeOwnerExistingParticipant(): array
    {
        $membershipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $currentUserId = $ownerId;
        $newOwnerId = $participantId;

        $membership = $this->createMembership($membershipId, $ownerId, $participantId, $taskOwnerId);

        return [$newOwnerId, $currentUserId, $membership];
    }

    private function createMembership(
        string $membershipId,
        string $ownerId,
        string $participantId,
        string $taskOwnerId,
        ProjectStatus $status = null
    ): Membership {
        if ($status === null) {
            $status = new ActiveProjectStatus();
        }

        return new Membership(
            new MembershipId($membershipId),
            $status,
            new UserId($ownerId),
            new UserIdCollection([
                new UserId($participantId)
            ]),
            new UserIdCollection([
                new UserId($taskOwnerId)
            ])
        );
    }
}
