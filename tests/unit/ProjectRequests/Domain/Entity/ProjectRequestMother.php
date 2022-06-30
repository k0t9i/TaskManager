<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\Collection\RequestCollection;
use App\ProjectRequests\Domain\Entity\Request;
use App\ProjectRequests\Domain\Entity\RequestProject;
use App\ProjectRequests\Domain\ValueObject\ConfirmedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\PendingRequestStatus;
use App\ProjectRequests\Domain\ValueObject\ProjectRequestId;
use App\ProjectRequests\Domain\ValueObject\RejectedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\ValueObject\ActiveProjectStatus;
use App\Shared\Domain\ValueObject\ClosedProjectStatus;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\ProjectStatus;
use App\Shared\Domain\ValueObject\UserId;
use Faker\Factory;
use Faker\Generator;

class ProjectRequestMother
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function createRequestByNonMember(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $this->faker->uuid();
        $requestId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function createRequestInClosedProjectRequest(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $this->faker->uuid();
        $requestId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            projectRequestStatus: new ClosedProjectStatus()
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function createRequestByParticipant(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $participantId;
        $requestId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function createRequestByOwner(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $ownerId;
        $requestId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function createRequestByUserWithPendingRequest(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $this->faker->uuid();
        $requestId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            [
                new RequestDTO(
                    $currentUserId,
                    new PendingRequestStatus()
                )
            ]
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function createRequestByUserWithConfirmedRequest(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $this->faker->uuid();
        $requestId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            [
                new RequestDTO(
                    $currentUserId,
                    new ConfirmedRequestStatus()
                )
            ]
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function createRequestByUserWithRejectedRequest(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $this->faker->uuid();
        $requestId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            [
                new RequestDTO(
                    $currentUserId,
                    new RejectedRequestStatus()
                )
            ]
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function changeRequestStatusByOwner(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $ownerId;
        $requestId = $this->faker->uuid();
        $requestOwnerId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            [
                new RequestDTO(
                    $requestOwnerId,
                    new PendingRequestStatus()
                )
            ]
        );

        return [$currentUserId, $projectRequest, $requestId, $requestOwnerId];
    }

    public function changeRequestStatusByNonOwner(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $this->faker->uuid();
        $requestId = $this->faker->uuid();
        $requestOwnerId = $this->faker->uuid();

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            [
                new RequestDTO(
                    $requestOwnerId,
                    new PendingRequestStatus()
                )
            ]
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function changeOwnerRequestStatus(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $ownerId;
        $requestId = $this->faker->uuid();
        $requestOwnerId = $ownerId;

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            [
                new RequestDTO(
                    $requestOwnerId,
                    new PendingRequestStatus()
                )
            ]
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    public function changeParticipantRequestStatus(): array
    {
        $projectRequestId = $this->faker->uuid();
        $ownerId = $this->faker->uuid();
        $participantId = $this->faker->uuid();
        $currentUserId = $ownerId;
        $requestId = $this->faker->uuid();
        $requestOwnerId = $participantId;

        $projectRequest = $this->createProjectRequest(
            $projectRequestId,
            $ownerId,
            $participantId,
            $requestId,
            [
                new RequestDTO(
                    $requestOwnerId,
                    new PendingRequestStatus()
                )
            ]
        );

        return [$currentUserId, $projectRequest, $requestId];
    }

    /**
     * @param string $projectRequestId
     * @param string $ownerId
     * @param string $participantId
     * @param RequestDTO[] $rawRequests
     * @param ProjectStatus|null $projectRequestStatus
     * @return RequestProject
     */
    private function createProjectRequest(
        string $projectRequestId,
        string $ownerId,
        string $participantId,
        string $requestId,
        array $rawRequests = [],
        ProjectStatus $projectRequestStatus = null,
    ): RequestProject {
        if ($projectRequestStatus === null) {
            $projectRequestStatus = new ActiveProjectStatus();
        }

        $requests = new RequestCollection();
        foreach ($rawRequests as $request) {
            $requests->add(new Request(
                new RequestId($requestId),
                new UserId($request->userId),
                $request->status,
                new DateTime()
            ));
        }
        $requests->flush();

        return new RequestProject(
            new ProjectRequestId($projectRequestId),
            $projectRequestStatus,
            new UserId($ownerId),
            new UserIdCollection([
                new UserId($participantId)
            ]),
            $requests
        );
    }
}
