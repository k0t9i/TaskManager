<?php
declare(strict_types=1);

namespace App\Users\Application\Handler;

use App\Shared\Domain\Bus\Query\QueryHandlerInterface;
use App\Shared\Domain\Exception\UserNotExistException;
use App\Shared\Domain\ValueObject\UserId;
use App\Users\Application\Query\GetUserQuery;
use App\Users\Application\Query\GetUserQueryResponse;
use App\Users\Domain\Repository\UserRepositoryInterface;

final class GetUserQueryHandler implements QueryHandlerInterface
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function __invoke(GetUserQuery $query): GetUserQueryResponse
    {
        $user = $this->userRepository->findById(new UserId($query->id));
        if ($user === null) {
            throw new UserNotExistException($query->id);
        }

        return GetUserQueryResponse::createFromEntity($user);
    }
}
