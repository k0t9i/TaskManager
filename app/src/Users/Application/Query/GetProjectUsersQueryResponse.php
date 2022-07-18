<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Users\Domain\DTO\UserListResponseDTO;

final class GetProjectUsersQueryResponse implements QueryResponseInterface
{
    /**
     * @var UserListResponseDTO[]
     */
    private readonly array $users;

    public function __construct(UserListResponseDTO... $users)
    {
        $this->users = $users;
    }

    /**
     * @return UserListResponseDTO[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
