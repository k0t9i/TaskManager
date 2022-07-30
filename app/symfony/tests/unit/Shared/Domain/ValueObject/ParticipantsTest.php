<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Domain\ValueObject;

use App\Projects\Domain\Exception\ProjectParticipantNotExistException;
use App\Shared\Domain\Collection\UserIdCollection;
use App\Shared\Domain\Exception\UserIsAlreadyParticipantException;
use App\Shared\Domain\ValueObject\Participants;
use App\Shared\Domain\ValueObject\Users\UserId;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

final class ParticipantsTest extends TestCase
{
    private Generator $faker;
    private string $participantId;
    private Participants $participants;
    private UserIdCollection $collection;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        $this->participantId = $this->faker->uuid();

        $users = [];
        for ($i = 0; $i < 10; ++$i) {
            $users[] = new UserId($this->faker->uuid());
        }

        $users[] = new UserId($this->participantId);

        $this->collection = new UserIdCollection($users);

        $this->participants = new Participants($this->collection);
    }

    public function testAdd(): void
    {
        $newId = $this->faker->uuid();

        $participants = $this->participants->add(new UserId($newId));

        self::assertTrue($participants->getCollection()->hashExists($newId));
        self::assertFalse($this->participants->getCollection()->hashExists($newId));
        self::assertNotSame($participants, $this->participants);
    }

    public function testIsParticipant(): void
    {
        self::assertTrue($this->participants->isParticipant(new UserId($this->participantId)));
        self::assertFalse($this->participants->isParticipant(new UserId($this->faker->uuid())));
    }

    public function testEnsureIsParticipant(): void
    {
        self::expectException(ProjectParticipantNotExistException::class);
        $this->participants->ensureIsParticipant(new UserId($this->faker->uuid()));
    }

    public function testGetCollection(): void
    {
        self::assertSame($this->collection, $this->participants->getCollection());
    }

    public function testEnsureIsNotParticipant(): void
    {
        self::expectException(UserIsAlreadyParticipantException::class);
        $this->participants->ensureIsNotParticipant(new UserId($this->participantId));
    }

    public function testRemove(): void
    {
        $participants = $this->participants->remove(new UserId($this->participantId));

        self::assertFalse($participants->getCollection()->hashExists($this->participantId));
        self::assertTrue($this->participants->getCollection()->hashExists($this->participantId));
        self::assertNotSame($participants, $this->participants);
    }
}
