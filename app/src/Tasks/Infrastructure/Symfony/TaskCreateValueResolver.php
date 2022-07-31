<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Symfony;

use App\Tasks\Infrastructure\Symfony\DTO\TaskCreateDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class TaskCreateValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return TaskCreateDTO::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $parameters = json_decode($request->getContent(), true);

        yield new TaskCreateDTO(
            $parameters['name'] ?? '',
            $parameters['brief'] ?? '',
            $parameters['description'] ?? '',
            $parameters['start_date'] ?? '',
            $parameters['finish_date'] ?? ''
        );
    }
}
