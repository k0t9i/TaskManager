<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\Entity\ProjectRequest;
use App\ProjectRequests\Domain\Entity\Request;
use App\ProjectRequests\Domain\Event\ProjectParticipantWasAddedEvent;
use App\ProjectRequests\Domain\Event\RequestStatusWasChangedEvent;
use App\ProjectRequests\Domain\Event\RequestWasCreatedEvent;
use App\ProjectRequests\Domain\Exception\ProjectRequestRequestNotExistsException;
use App\ProjectRequests\Domain\Exception\UserAlreadyHasNonRejectedProjectRequestRequestException;
use App\ProjectRequests\Domain\ValueObject\ConfirmedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RejectedRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Exception\ModificationDeniedException;
use App\Shared\Domain\Exception\UserIsAlreadyOwnerException;
use App\Shared\Domain\Exception\UserIsAlreadyParticipantException;
use App\Shared\Domain\Exception\UserIsNotOwnerException;
use App\Shared\Domain\ValueObject\UserId;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class ProjectRequestTest extends TestCase
{
    private ProjectRequestMother $mother;

    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->mother = new ProjectRequestMother();
    }

    public function testCreateRequestByNonMember()
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->createRequestByNonMember();

        $projectRequest->createRequest(
            new RequestId($requestId),
            new UserId($currentUserId)
        );

        $this->createRequestPositiveAssertions($projectRequest, $requestId, $currentUserId, 1);
    }

    public function testCreateRequestByUserWithRejectedRequest()
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest] = $this->mother->createRequestByUserWithRejectedRequest();
        $requestId = $this->faker->uuid();

        $projectRequest->createRequest(
            new RequestId($requestId),
            new UserId($currentUserId)
        );

        $this->createRequestPositiveAssertions($projectRequest, $requestId, $currentUserId, 2);
    }

    public function testCreateRequestInClosedProjectRequest()
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->createRequestInClosedProjectRequest();

        self::expectException(ModificationDeniedException::class);
        $projectRequest->createRequest(
            new RequestId($requestId),
            new UserId($currentUserId)
        );
    }

    public function testCreateRequestByParticipant()
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->createRequestByParticipant();

        self::expectException(UserIsAlreadyParticipantException::class);
        $projectRequest->createRequest(
            new RequestId($requestId),
            new UserId($currentUserId)
        );
    }

    public function testCreateRequestByOwner()
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->createRequestByOwner();

        self::expectException(UserIsAlreadyOwnerException::class);
        $projectRequest->createRequest(
            new RequestId($requestId),
            new UserId($currentUserId)
        );
    }

    public function testCreateRequestByUserWithPendingRequest()
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->createRequestByUserWithPendingRequest();

        self::expectException(UserAlreadyHasNonRejectedProjectRequestRequestException::class);
        $projectRequest->createRequest(
            new RequestId($requestId),
            new UserId($currentUserId)
        );
    }

    public function testCreateRequestByUserWithConfirmedRequest()
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->createRequestByUserWithConfirmedRequest();

        self::expectException(UserAlreadyHasNonRejectedProjectRequestRequestException::class);
        $projectRequest->createRequest(
            new RequestId($requestId),
            new UserId($currentUserId)
        );
    }

    public function testRejectRequestByOwner(): void
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->changeRequestStatusByOwner();
        $status = new RejectedRequestStatus();

        $projectRequest->changeRequestStatus(
            new RequestId($requestId),
            $status,
            new UserId($currentUserId)
        );

        /** @var Request $request */
        $request = $projectRequest->getRequests()->get((new RequestId($requestId))->getHash());
        self::assertNotEmpty($request);
        self::assertInstanceOf(RejectedRequestStatus::class, $request->getStatus());
        $events = $projectRequest->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        /** @var RequestStatusWasChangedEvent $event */
        $event = $events[0];
        self::assertInstanceOf(RequestStatusWasChangedEvent::class, $event);
        self::assertEquals($projectRequest->getId()->value, $event->aggregateId);
        self::assertEquals($status->getScalar(), $event->status);
    }

    public function testConfirmRequestByOwner(): void
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId, $requestOwnerId] = $this->mother->changeRequestStatusByOwner();
        $status = new ConfirmedRequestStatus();

        $projectRequest->changeRequestStatus(
            new RequestId($requestId),
            $status,
            new UserId($currentUserId)
        );

        /** @var Request $request */
        $request = $projectRequest->getRequests()->get((new RequestId($requestId))->getHash());
        self::assertNotEmpty($request);
        self::assertInstanceOf(ConfirmedRequestStatus::class, $request->getStatus());
        $events = $projectRequest->releaseEvents();
        self::assertCount(2, $events);
        self::assertTrue(isset($events[0]));
        /** @var ProjectParticipantWasAddedEvent $event */
        $event = $events[0];
        self::assertInstanceOf(ProjectParticipantWasAddedEvent::class, $event);
        self::assertEquals($projectRequest->getId()->value, $event->aggregateId);
        self::assertEquals($requestOwnerId, $event->participantId);

        self::assertTrue(isset($events[1]));
        /** @var RequestStatusWasChangedEvent $event */
        $event = $events[1];
        self::assertInstanceOf(RequestStatusWasChangedEvent::class, $event);
        self::assertEquals($projectRequest->getId()->value, $event->aggregateId);
        self::assertEquals($status->getScalar(), $event->status);
    }

    public function testConfirmRequestByNonOwner(): void
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->changeRequestStatusByNonOwner();
        $status = new ConfirmedRequestStatus();

        self::expectException(UserIsNotOwnerException::class);
        $projectRequest->changeRequestStatus(
            new RequestId($requestId),
            $status,
            new UserId($currentUserId)
        );
    }

    public function testConfirmNonExistingRequest(): void
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest] = $this->mother->changeRequestStatusByOwner();
        $status = new ConfirmedRequestStatus();

        self::expectException(ProjectRequestRequestNotExistsException::class);
        $projectRequest->changeRequestStatus(
            new RequestId($this->faker->uuid()),
            $status,
            new UserId($currentUserId)
        );
    }

    public function testConfirmOwnerRequest(): void
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->changeOwnerRequestStatus();
        $status = new ConfirmedRequestStatus();

        self::expectException(UserIsAlreadyOwnerException::class);
        $projectRequest->changeRequestStatus(
            new RequestId($requestId),
            $status,
            new UserId($currentUserId)
        );
    }

    public function testConfirmParticipantRequest(): void
    {
        /** @var ProjectRequest $projectRequest */
        [$currentUserId, $projectRequest, $requestId] = $this->mother->changeParticipantRequestStatus();
        $status = new ConfirmedRequestStatus();

        self::expectException(UserIsAlreadyParticipantException::class);
        $projectRequest->changeRequestStatus(
            new RequestId($requestId),
            $status,
            new UserId($currentUserId)
        );
    }

    private function createRequestPositiveAssertions(
        ProjectRequest $projectRequest,
        string $requestId,
        string $currentUserId,
        int $requestCount
    ): void {
        self::assertCount($requestCount, $projectRequest->getRequests());
        self::assertTrue($projectRequest->getRequests()->hashExists((new RequestId($requestId))->getHash()));
        $events = $projectRequest->releaseEvents();
        self::assertCount(1, $events);
        self::assertTrue(isset($events[0]));
        /** @var RequestWasCreatedEvent $event */
        $event = $events[0];
        self::assertInstanceOf(RequestWasCreatedEvent::class, $event);
        self::assertEquals($projectRequest->getId()->value, $event->aggregateId);
        self::assertEquals($requestId, $event->requestId);
        self::assertEquals($currentUserId, $event->userId);
    }
}

