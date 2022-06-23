<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Application\Handler;

use App\ProjectRequests\Domain\Entity\ProjectRequest;
use App\ProjectRequests\Domain\Repository\ProjectRequestRepositoryInterface;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Bus\Event\DomainEvent;
use App\Shared\Domain\Bus\Event\EventBusInterface;
use App\Shared\Domain\ValueObject\UserId;

trait ChangeStatusSetUpTrait
{
    private function setUpHandlerParams(string $statusClass, string $requestId, string $userId): array
    {
        $project = $this->getMockBuilder(ProjectRequest::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['changeStatus', 'releaseEvents'])
            ->getMock();
        $project->expects(self::once())
            ->method('changeStatus')
            ->with(
                self::equalTo(new RequestId($requestId)),
                self::isInstanceOf($statusClass),
                self::equalTo(new UserId($userId))
            );
        $releaseEventsResult = [
            new DomainEvent()
        ];
        $project->expects(self::once())
            ->method('releaseEvents')
            ->willReturn($releaseEventsResult);

        $projectRepository = $this->getMockForAbstractClass(
            ProjectRequestRepositoryInterface::class,
            mockedMethods: ['findByRequestId', 'update']
        );
        $projectRepository->expects(self::once())
            ->method('findByRequestId')
            ->with(new RequestId($requestId))
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

        return [$projectRepository, $eventBus];
    }
}