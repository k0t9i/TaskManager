<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Application\Bus\Query\QueryResponseInterface;
use App\Shared\Application\DTO\PaginationDTO;
use App\Users\Domain\Entity\UserProjection;

final class GetProjectUsersQueryResponse implements QueryResponseInterface
{
    private readonly PaginationDTO $pagination;
    /**
     * @var UserProjection[]
     */
    private readonly array $items;

    public function __construct(PaginationDTO $pagination, UserProjection... $items)
    {
        $this->pagination = $pagination;
        $this->items = $items;
    }

    public function getPagination(): PaginationDTO
    {
        return $this->pagination;
    }

    /**
     * @return UserProjection[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
