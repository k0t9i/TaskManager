<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectMemberships\Application\Handler;

use App\ProjectMemberships\Application\CQ\ChangeProjectOwnerCommand;
use App\ProjectMemberships\Application\Handler\ChangeProjectOwnerCommandHandler;
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

class ChangeProjectOwnerCommandHandlerTest extends TestCase
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
        $newOwnerId = $this->faker->uuid();

        $newOwner = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();
        $newOwner->expects(self::once())
            ->method('getId')
            ->willReturn(
                new UserId($newOwnerId)
            );

        $membership = $this->getMockBuilder(Membership::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['changeOwner', 'releaseEvents'])
            ->getMock();
        $membership->expects(self::once())
            ->method('changeOwner')
            ->with(
                self::equalTo(new UserId($newOwnerId)),
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
            ->with(new UserId($newOwnerId))
            ->willReturn($newOwner);

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

        $handler = new ChangeProjectOwnerCommandHandler(
            $membershipRepository,
            $userRepository,
            $eventBus
        );
        $command = new ChangeProjectOwnerCommand($membershipId, $newOwnerId, $userId);

        $handler->__invoke($command);
    }

    public function testNonExistingOwner(): void
    {
        $membershipId = $this->faker->uuid();
        $userId = $this->faker->uuid();
        $newOwnerId = $this->faker->uuid();

        $membership = $this->getMockBuilder(Membership::class)
            ->disableOriginalConstructor()
            ->getMock();

        $userRepository = $this->getMockForAbstractClass(
            UserRepositoryInterface::class,
            mockedMethods: ['findById']
        );
        $userRepository->expects(self::once())
            ->method('findById')
            ->with(new UserId($newOwnerId))
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

        $handler = new ChangeProjectOwnerCommandHandler(
            $membershipRepository,
            $userRepository,
            $eventBus
        );
        $command = new ChangeProjectOwnerCommand($membershipId, $newOwnerId, $userId);

        self::expectException(UserNotExistException::class);
        $handler->__invoke($command);
    }

    public function testNonExistingMembership(): void
    {
        $membershipId = $this->faker->uuid();
        $userId = $this->faker->uuid();
        $newOwnerId = $this->faker->uuid();

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

        $handler = new ChangeProjectOwnerCommandHandler(
            $membershipRepository,
            $userRepository,
            $eventBus
        );
        $command = new ChangeProjectOwnerCommand($membershipId, $newOwnerId, $userId);

        self::expectException(ProjectMembershipNotExistException::class);
        $handler->__invoke($command);
    }
}

