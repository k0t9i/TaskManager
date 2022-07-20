<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Application\DTO\PaginationDTO;
use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Users\Domain\DTO\UserResponseDTO;

final class GetProjectUsersQueryResponse implements QueryResponseInterface
{
    private readonly PaginationDTO $pagination;
    /**
     * @var UserResponseDTO[]
     */
    private readonly array $items;

    public function __construct(PaginationDTO $pagination, UserResponseDTO... $items)
    {
        $this->pagination = $pagination;
        $this->items = $items;
    }

    public function getPagination(): PaginationDTO
    {
        return $this->pagination;
    }

    /**
     * @return UserResponseDTO[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
