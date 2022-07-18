<?php
declare(strict_types=1);

namespace App\Users\Infrastructure\Controller;

use App\Shared\Domain\Bus\Command\CommandBusInterface;
use App\Shared\Domain\Bus\Query\QueryBusInterface;
use App\Users\Application\Command\UpdateProfileCommand;
use App\Users\Application\Query\GetProfileQuery;
use App\Users\Application\Query\GetProfileQueryResponse;
use App\Users\Application\Query\GetProjectUsersQuery;
use App\Users\Application\Query\GetProjectUsersQueryResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'security.')]
final class UserController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    #[Route('/profile/', name: 'updateProfile', methods: ['PATCH'])]
    public function updateProfile(Request $request): JsonResponse
    {
        $parameters = json_decode($request->getContent(), true);

        $this->commandBus->dispatch(UpdateProfileCommand::createFromRequest($parameters));
        return new JsonResponse();
    }

    #[Route('/profile/', name: 'getProfile', methods: ['GET'])]
    public function getProfile(): JsonResponse
    {
        /** @var GetProfileQueryResponse $envelop */
        $envelop = $this->queryBus->dispatch(new GetProfileQuery());
        return new JsonResponse($envelop->getProfile());
    }

    #[Route('/in-project/{id}/', name: 'getAllInProject', methods: ['GET'])]
    public function getAllInProject(string $id): JsonResponse
    {
        /** @var GetProjectUsersQueryResponse $envelop */
        $envelop = $this->queryBus->dispatch(new GetProjectUsersQuery($id));
        return new JsonResponse($envelop->getUsers());
    }
}
