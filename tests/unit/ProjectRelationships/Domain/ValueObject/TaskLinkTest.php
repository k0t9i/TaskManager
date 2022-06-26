<?php
declare(strict_types=1);

namespace App\Tests\unit\ProjectRelationships\Domain\ValueObject;

use App\ProjectRelationships\Domain\Exception\TasksOfProjectRelationshipTaskLinkAreEqualException;
use App\ProjectRelationships\Domain\ValueObject\RelationshipTaskId;
use App\ProjectRelationships\Domain\ValueObject\TaskLink;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase;

class TaskLinkTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        BypassFinals::setWhitelist(['*/src/ProjectRelationships/*']);
        BypassFinals::enable();
        $this->faker = Factory::create();
    }

    public function testHashOfOppositeLinksIsEqual()
    {
        $firstId = $this->faker->uuid();
        $secondId = $this->faker->uuid();

        $firstLink = new TaskLink(
            new RelationshipTaskId($firstId),
            new RelationshipTaskId($secondId),
        );
        $secondLink = new TaskLink(
            new RelationshipTaskId($secondId),
            new RelationshipTaskId($firstId),
        );
        self::assertEquals($firstLink->getHash(), $secondLink->getHash());
    }

    public function testLinkForIdenticalIds()
    {
        $id = $this->faker->uuid();

        self::expectException(TasksOfProjectRelationshipTaskLinkAreEqualException::class);
        new TaskLink(
            new RelationshipTaskId($id),
            new RelationshipTaskId($id),
        );
    }
}

