<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Application\Handler;

use App\ProjectRequests\Application\CQ\CreateRequestToProjectCommand;
use App\ProjectRequests\Application\Handler\CreateRequestToProjectCommandHandler;
use App\ProjectRequests\Domain\Entity\ProjectRequest;
use App\ProjectRequests\Domain\Repository\ProjectRequestRepositoryInterface;
use App\ProjectRequests\Domain\ValueObject\ProjectRequestId;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\UuidGeneratorInterface;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Domain\Entity\User;
use App\Users\Domain\Repository\UserRepositoryInterface;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class CreateRequestToProjectCommandHandlerTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/*']);
        BypassFinals::enable();
        $this->faker = Factory::create();
    }

    public function testInvoke()
    {
        $projectId = $this->faker->uuid();
        $userId = $this->faker->uuid();
        $newUuid = $this->faker->uuid();

        $user = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getId'])
            ->getMock();
        $user->expects(self::once())
            ->method('getId')
            ->willReturn(
                new UserId($userId)
            );

        $project = $this->getMockBuilder(ProjectRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createRequest', 'releaseEvents'])
            ->getMock();
        $project->expects(self::once())
            ->method('createRequest')
            ->with(
                self::equalTo(new RequestId($newUuid)),
                self::equalTo(new UserId($userId))
            );
        $releaseEventsResult = [
            new DomainEvent()
        ];
        $project->expects(self::once())
            ->method('releaseEvents')
            ->willReturn($releaseEventsResult);


        $uuidGenerator = $this->getMockForAbstractClass(
            UuidGeneratorInterface::class,
            mockedMethods: ['generate']
        );
        $uuidGenerator->expects(self::once())
            ->method('generate')
            ->willReturn($newUuid);

        $userRepository = $this->getMockForAbstractClass(
            UserRepositoryInterface::class,
            mockedMethods: ['getById']
        );
        $userRepository->expects(self::once())
            ->method('getById')
            ->with(new UserId($userId))
            ->willReturn($user);

        $projectRepository = $this->getMockForAbstractClass(
            ProjectRequestRepositoryInterface::class,
            mockedMethods: ['findByRequestId', 'update']
        );
        $projectRepository->expects(self::once())
            ->method('getById')
            ->with(new ProjectRequestId($projectId))
            ->willReturn($project);
        $projectRepository->expects(self::once())
            ->method('update')
            ->with($project);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class,
            mockedMethods: ['dispatch']
        );
        $eventBus->expects(self::once())
            ->method('dispatch')
            ->with(...$releaseEventsResult);


        $handler = new CreateRequestToProjectCommandHandler(
            $projectRepository,
            $userRepository,
            $uuidGenerator,
            $eventBus
        );

        $command = new CreateRequestToProjectCommand($projectId, $userId);
        $handler->__invoke($command);
    }
}

