<?php
declare(strict_types=1);

namespace App\Users\Application\Query;

use App\Shared\Domain\Bus\Query\QueryResponseInterface;
use App\Users\Domain\DTO\UserResponseDTO;

final class GetProjectUsersQueryResponse implements QueryResponseInterface
{
    /**
     * @var UserResponseDTO[]
     */
    private readonly array $users;

    public function __construct(UserResponseDTO... $users)
    {
        $this->users = $users;
    }

    /**
     * @return UserResponseDTO[]
     */
    public function getUsers(): array
    {
        return $this->users;
    }
}
