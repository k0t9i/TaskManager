<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRelationships\Domain\Entity;

use App\ProjectRelationships\Domain\Collection\RelationshipTaskCollection;
use App\ProjectRelationships\Domain\Collection\TaskLinkCollection;
use App\ProjectRelationships\Domain\Entity\Relationship;
use App\ProjectRelationships\Domain\Entity\RelationshipTask;
use App\ProjectRelationships\Domain\ValueObject\RelationshipId;
use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;
use App\ProjectRelationships\Domain\ValueObject\TaskLink;
use App\Shared\Domain\ValueObject\ActiveProjectStatus;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;
use Faker\Factory;
use Faker\Generator;

final class RelationshipMother
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function createLinkByOwner(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $ownerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3
        );

        return [$taskId1, $taskId2, $currentUserId, $relationship];
    }

    public function createLinkByTaskOwner(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $taskOwnerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3
        );

        return [$taskId1, $taskId2, $currentUserId, $relationship];
    }

    public function createLinkInClosedRelationship(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $ownerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3,
            new ClosedProjectStatus()
        );

        return [$taskId1, $taskId2, $currentUserId, $relationship];
    }

    public function createExistingLink(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $ownerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3
        );

        return [$taskId1, $taskId3, $currentUserId, $relationship];
    }

    public function deleteLinkByOwner(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $ownerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3
        );

        return [$taskId1, $taskId3, $currentUserId, $relationship];
    }

    public function deleteLinkByTaskOwner(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $taskOwnerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3
        );

        return [$taskId1, $taskId3, $currentUserId, $relationship];
    }

    public function deleteLinkInClosedRelationship(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $ownerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3,
            new ClosedProjectStatus()
        );

        return [$taskId1, $taskId3, $currentUserId, $relationship];
    }

    public function deleteNonExistingLink(): array
    {
        $relationshipId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $taskOwnerId = $this->faker->uuid();
        $taskId1 = $this->faker->uuid();
        $taskId2 = $this->faker->uuid();
        $taskId3 = $this->faker->uuid();
        $currentUserId = $ownerId;

        $relationship = $this->createRelationship(
            $relationshipId,
            $ownerId,
            $taskOwnerId,
            $taskId1,
            $taskId2,
            $taskId3
        );

        return [$taskId1, $taskId2, $currentUserId, $relationship];
    }

    private function createRelationship(
        string $relationshipId,
        string $ownerId,
        string $taskOwnerId,
        string $taskId1,
        string $taskId2,
        string $taskId3,
        ProjectStatus $status = null
    ): Relationship {
        if ($status === null) {
            $status = new ActiveProjectStatus();
        }

        return new Relationship(
            new RelationshipId($relationshipId),
            $status,
            new UserId($ownerId),
            new RelationshipTaskCollection([
                new RelationshipTask(
                    new RelationshipTaskId($taskId1),
                    new UserId($taskOwnerId),
                ),
                new RelationshipTask(
                    new RelationshipTaskId($taskId2),
                    new UserId($taskOwnerId),
                ),
                new RelationshipTask(
                    new RelationshipTaskId($taskId3),
                    new UserId($taskOwnerId),
                )
            ]),
            new TaskLinkCollection([
                new TaskLink(
                    new RelationshipTaskId($taskId1),
                    new RelationshipTaskId($taskId3),
                )
            ])
        );
    }
}
