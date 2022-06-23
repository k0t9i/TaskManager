<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Domain\Entity;

use App\ProjectRequests\Domain\Entity\Request;
use App\ProjectRequests\Domain\ValueObject\PendingRequestStatus;
use App\ProjectRequests\Domain\ValueObject\RequestId;
use App\ProjectRequests\Domain\ValueObject\RequestStatus;
use App\Users\Domain\ValueObject\UserId;
use DG\BypassFinals;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class RequestTest extends TestCase
{
    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/*']);
        BypassFinals::enable();
    }

    public function testCreate(): void
    {
        $idRaw = '208ca6aa-d3d5-4bab-88d0-80304310d3a8';
        $userIdRaw = '308ca6aa-d3d5-4bab-88d0-80304310d3a8';

        $id = new RequestId($idRaw);
        $userId = new UserId($userIdRaw);

        $request = Request::create($id, $userId);

        self::assertTrue($id->isEqual($request->getId()));
        self::assertTrue($userId->isEqual($request->getUserId()));
        self::assertInstanceOf(PendingRequestStatus::class, $request->getStatus());
    }

    public function testChangeStatus(): void
    {
        $status = self::getMockBuilder(RequestStatus::class)
            ->disableOriginalConstructor()
            ->getMock();
        $status->expects(self::once())
            ->method('ensureCanBeChangedTo');

        /** @var Request|MockObject $request */
        $request = self::getMockBuilder(Request::class)
            ->onlyMethods(['getStatus'])
            ->disableOriginalConstructor()
            ->getMock();
        $request->expects(self::once())
            ->method('getStatus')
            ->willReturn($status);

        $request->changeStatus($status);

        $reflection = new ReflectionProperty(Request::class, 'status');
        $value = $reflection->getValue($request);
        self::assertInstanceOf(get_class($status), $value);
    }
}
