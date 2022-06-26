<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRequests\Application\Handler;

use App\ProjectRequests\Application\CQ\ConfirmRequestCommand;
use App\ProjectRequests\Application\Handler\ConfirmRequestCommandHandler;
use App\ProjectRequests\Domain\ValueObject\ConfirmedRequestStatus;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class ConfirmRequestCommandHandlerTest extends TestCase
{
    use ChangeStatusSetUpTrait;

    private Generator $faker;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/ProjectRequests/*']);
        BypassFinals::enable();
        $this->faker = Factory::create();
    }

    public function testInvoke()
    {
        $requestId = $this->faker->uuid();
        $userId = $this->faker->uuid();
        $command = new ConfirmRequestCommand($requestId, $userId);

        $handler = new ConfirmRequestCommandHandler(...$this->setUpHandlerParams(
            ConfirmedRequestStatus::class,
            $requestId,
            $userId
        ));
        $handler->__invoke($command);
    }
}

