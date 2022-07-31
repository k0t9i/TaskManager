<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Symfony;

use App\Projects\Infrastructure\Symfony\DTO\ProjectUpdateDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ProjectUpdateValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ProjectUpdateDTO::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $parameters = json_decode($request->getContent(), true);

        yield new ProjectUpdateDTO(
            $parameters['name'] ?? null,
            $parameters['description'] ?? null,
            $parameters['finish_date'] ?? null
        );
    }
}
