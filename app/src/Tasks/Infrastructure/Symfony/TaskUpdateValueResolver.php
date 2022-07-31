<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Symfony;

use App\Tasks\Infrastructure\Symfony\DTO\TaskUpdateDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class TaskUpdateValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return TaskUpdateDTO::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $parameters = json_decode($request->getContent(), true);

        yield new TaskUpdateDTO(
            $parameters['name'] ?? null,
            $parameters['brief'] ?? null,
            $parameters['description'] ?? null,
            $parameters['start_date'] ?? null,
            $parameters['finish_date'] ?? null
        );
    }
}
