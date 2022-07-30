<?php

declare(strict_types=1);

namespace App\Tests\unit\Shared\Application\DTO;

use App\Shared\Application\DTO\PaginationItemsDTO;
use App\Shared\Application\Service\Pagination;
use DG\BypassFinals;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PaginationItemsDTOTest extends TestCase
{
    private Generator $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
        BypassFinals::enable();
    }

    public function testCreate()
    {
        $count = mt_rand(0, 100);
        $current = mt_rand(0, 100);
        $prev = mt_rand(0, 100);
        $next = mt_rand(0, 100);
        /** @var Pagination|MockObject $pagination */
        $pagination = self::getMockBuilder(Pagination::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
                'getTotalPageCount', 'getCurrentPage', 'getPrevPage', 'getNextPage',
            ])
            ->getMock();
        $pagination->expects(self::once())
            ->method('getTotalPageCount')
            ->willReturn($count);
        $pagination->expects(self::once())
            ->method('getCurrentPage')
            ->willReturn($current);
        $pagination->expects(self::once())
            ->method('getPrevPage')
            ->willReturn($prev);
        $pagination->expects(self::once())
            ->method('getNextPage')
            ->willReturn($next);

        $items = $this->faker->words(20);

        $dto = PaginationItemsDTO::create($pagination, $items);

        self::assertEquals($items, $dto->items);
        self::assertEquals($count, $dto->total);
        self::assertEquals($current, $dto->current);
        self::assertEquals($prev, $dto->prev);
        self::assertEquals($next, $dto->next);
    }
}
