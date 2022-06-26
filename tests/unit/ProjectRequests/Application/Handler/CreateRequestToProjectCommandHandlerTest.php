<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Application\Handler;

use App\ProjectRequests\Application\CQ\CreateRequestToProjectCommand;
use App\ProjectRequests\Application\Handler\CreateRequestToProjectCommandHandler;
use App\ProjectRequests\Domain\Entity\ProjectRequest;
use App\ProjectRequests\Domain\Exception\ProjectRequestNotExistsException;
use App\ProjectRequests\Domain\Repository\ProjectRequestRepositoryInterface;
use App\ProjectRequests\Domain\ValueObject\ProjectRequestId;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\Exception\UserNotExistException;
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
        BypassFinals::setWhitelist(['*/src/ProjectRequests/*']);
        BypassFinals::enable();
        $this->faker = Factory::create();
    }

    public function testPositive()
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
            $this->getMockForAbstractClass(DomainEvent::class, callOriginalConstructor: false)
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
            mockedMethods: ['findById']
        );
        $userRepository->expects(self::once())
            ->method('findById')
            ->with(new UserId($userId))
            ->willReturn($user);

        $projectRepository = $this->getMockForAbstractClass(
            ProjectRequestRepositoryInterface::class,
            mockedMethods: ['findById', 'update']
        );
        $projectRepository->expects(self::once())
            ->method('findById')
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

    public function testNonExistingUser()
    {
        $projectId = $this->faker->uuid();
        $userId = $this->faker->uuid();

        $project = $this->getMockBuilder(ProjectRequest::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uuidGenerator = $this->getMockForAbstractClass(
            UuidGeneratorInterface::class
        );

        $userRepository = $this->getMockForAbstractClass(
            UserRepositoryInterface::class,
            mockedMethods: ['findById']
        );
        $userRepository->expects(self::once())
            ->method('findById')
            ->with(new UserId($userId))
            ->willReturn(null);

        $projectRepository = $this->getMockForAbstractClass(
            ProjectRequestRepositoryInterface::class
        );
        $projectRepository->expects(self::once())
            ->method('findById')
            ->with(new ProjectRequestId($projectId))
            ->willReturn($project);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class
        );

        $handler = new CreateRequestToProjectCommandHandler(
            $projectRepository,
            $userRepository,
            $uuidGenerator,
            $eventBus
        );

        $command = new CreateRequestToProjectCommand($projectId, $userId);

        self::expectException(UserNotExistException::class);
        $handler->__invoke($command);
    }

    public function testNonExistingProjectRequest()
    {
        $projectId = $this->faker->uuid();
        $userId = $this->faker->uuid();

        $uuidGenerator = $this->getMockForAbstractClass(
            UuidGeneratorInterface::class
        );

        $userRepository = $this->getMockForAbstractClass(
            UserRepositoryInterface::class
        );

        $projectRepository = $this->getMockForAbstractClass(
            ProjectRequestRepositoryInterface::class,
            mockedMethods: ['findById']
        );
        $projectRepository->expects(self::once())
            ->method('findById')
            ->with(new ProjectRequestId($projectId))
            ->willReturn(null);

        $eventBus = $this->getMockForAbstractClass(
            EventBusInterface::class
        );

        $handler = new CreateRequestToProjectCommandHandler(
            $projectRepository,
            $userRepository,
            $uuidGenerator,
            $eventBus
        );

        $command = new CreateRequestToProjectCommand($projectId, $userId);

        self::expectException(ProjectRequestNotExistsException::class);
        $handler->__invoke($command);
    }
}

