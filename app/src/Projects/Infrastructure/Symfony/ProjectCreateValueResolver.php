<?php

declare(strict_types=1);

namespace App\Projects\Infrastructure\Symfony;

use App\Projects\Infrastructure\Symfony\DTO\ProjectCreateDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ProjectCreateValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ProjectCreateDTO::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $parameters = json_decode($request->getContent(), true);

        yield new ProjectCreateDTO(
            $parameters['name'] ?? '',
            $parameters['description'] ?? '',
            $parameters['finish_date'] ?? ''
        );
    }
}
