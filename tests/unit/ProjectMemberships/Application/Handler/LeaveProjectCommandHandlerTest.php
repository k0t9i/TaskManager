<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectMemberships\Application\Handler;

use App\ProjectMemberships\Application\Command\LeaveProjectCommand;
use App\ProjectMemberships\Application\Handler\LeaveProjectCommandHandler;
use App\ProjectMemberships\Domain\Entity\Membership;
use App\ProjectMemberships\Domain\Exception\ProjectMembershipNotExistException;
use App\ProjectMemberships\Domain\Repository\MembershipRepositoryInterface;
use App\ProjectMemberships\Domain\ValueObject\MembershipId;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class LeaveProjectCommandHandlerTest extends TestCase
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

        $membership = $this->getMockBuilder(Membership::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['removeParticipant', 'releaseEvents'])
            ->getMock();
        $membership->expects(self::once())
            ->method('removeParticipant')
            ->with(
                self::equalTo(new UserId($userId)),
                self::equalTo(new UserId($userId))
            );
        $releaseEventsResult = [
            $this->getMockForAbstractClass(DomainEvent::class, callOriginalConstructor: false)
        ];
        $membership->expects(self::once())
            ->method('releaseEvents')
            ->willReturn($releaseEventsResult);

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

        $handler = new LeaveProjectCommandHandler(
            $membershipRepository,
            $eventBus
        );
        $command = new LeaveProjectCommand($membershipId, $userId);

        $handler->__invoke($command);
    }

    public function testNonExistingMembership(): void
    {
        $membershipId = $this->faker->uuid();
        $userId = $this->faker->uuid();

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

        $handler = new LeaveProjectCommandHandler(
            $membershipRepository,
            $eventBus
        );
        $command = new LeaveProjectCommand($membershipId, $userId);

        self::expectException(ProjectMembershipNotExistException::class);
        $handler->__invoke($command);
    }
}

