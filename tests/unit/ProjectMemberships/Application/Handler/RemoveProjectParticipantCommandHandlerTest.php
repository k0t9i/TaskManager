<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectMemberships\Application\Handler;

use App\ProjectMemberships\Application\CQ\RemoveProjectParticipantCommand;
use App\ProjectMemberships\Application\Handler\RemoveProjectParticipantCommandHandler;
use App\ProjectMemberships\Domain\Entity\Membership;
use App\ProjectMemberships\Domain\Exception\ProjectMembershipNotExistException;
use App\ProjectMemberships\Domain\Repository\MembershipRepositoryInterface;
use App\ProjectMemberships\Domain\ValueObject\MembershipId;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class RemoveProjectParticipantCommandHandlerTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/ProjectMemberships/*']);
        BypassFinals::enable();
        $this->faker = Factory::create();
    }

    public function testPositive()
    {
        $membershipId = $this->faker->uuid();
        $userId = $this->faker->uuid();
        $participantId = $this->faker->uuid();

        $participant = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();
        $participant->expects(self::once())
            ->method('getId')
            ->willReturn(
                new UserId($participantId)
            );

        $membership = $this->getMockBuilder(Membership::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['removeParticipant', 'releaseEvents'])
            ->getMock();
        $membership->expects(self::once())
            ->method('removeParticipant')
            ->with(
                self::equalTo(new UserId($participantId)),
                self::equalTo(new UserId($userId))
            );
        $releaseEventsResult = [
            $this->getMockForAbstractClass(DomainEvent::class, callOriginalConstructor: false)
        ];
        $membership->expects(self::once())
            ->method('releaseEvents')
            ->willReturn($releaseEventsResult);

        $userRepository = $this->getMockForAbstractClass(
            UserRepositoryInterface::class,
            mockedMethods: ['findById']
        );
        $userRepository->expects(self::once())
            ->method('findById')
            ->with(new UserId($participantId))
            ->willReturn($participant);

        $membershipRepository = $this->getMockForAbstractClass(
            MembershipRepositoryInterface::class,
            mockedMethods: ['findById', 'update']
        );
        $membershipRepository->expects(self::once())
            ->method('findById')
            ->with(new MembershipId($membershipId))
            ->willReturn($membership);
        $membershipRepository->expects(self::once())
            ->method('update')
            ->with($membership);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class,
            mockedMethods: ['dispatch']
        );
        $eventBus->expects(self::once())
            ->method('dispatch')
            ->with(...$releaseEventsResult);

        $handler = new RemoveProjectParticipantCommandHandler(
            $membershipRepository,
            $userRepository,
            $eventBus
        );
        $command = new RemoveProjectParticipantCommand($membershipId, $participantId, $userId);

        $handler->__invoke($command);
    }

    public function testNonExistingParticipant(): void
    {
        $membershipId = $this->faker->uuid();
        $userId = $this->faker->uuid();
        $participantId = $this->faker->uuid();

        $membership = $this->getMockBuilder(Membership::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userRepository = $this->getMockForAbstractClass(
            UserRepositoryInterface::class,
            mockedMethods: ['findById']
        );
        $userRepository->expects(self::once())
            ->method('findById')
            ->with(new UserId($participantId))
            ->willReturn(null);

        $membershipRepository = $this->getMockForAbstractClass(
            MembershipRepositoryInterface::class,
            mockedMethods: ['findById']
        );
        $membershipRepository->expects(self::once())
            ->method('findById')
            ->with(new MembershipId($membershipId))
            ->willReturn($membership);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class,
        );

        $handler = new RemoveProjectParticipantCommandHandler(
            $membershipRepository,
            $userRepository,
            $eventBus
        );
        $command = new RemoveProjectParticipantCommand($membershipId, $participantId, $userId);

        self::expectException(UserNotExistException::class);
        $handler->__invoke($command);
    }

    public function testNonExistingMembership(): void
    {
        $membershipId = $this->faker->uuid();
        $userId = $this->faker->uuid();
        $participantId = $this->faker->uuid();

        $userRepository = $this->getMockForAbstractClass(
            UserRepositoryInterface::class
        );

        $membershipRepository = $this->getMockForAbstractClass(
            MembershipRepositoryInterface::class,
            mockedMethods: ['findById']
        );
        $membershipRepository->expects(self::once())
            ->method('findById')
            ->with(new MembershipId($membershipId))
            ->willReturn(null);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class,
        );

        $handler = new RemoveProjectParticipantCommandHandler(
            $membershipRepository,
            $userRepository,
            $eventBus
        );
        $command = new RemoveProjectParticipantCommand($membershipId, $participantId, $userId);

        self::expectException(ProjectMembershipNotExistException::class);
        $handler->__invoke($command);
    }
}

