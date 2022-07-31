<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Symfony;

use App\Users\Infrastructure\Symfony\DTO\ProfileUpdateDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ProfileUpdateValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ProfileUpdateDTO::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $parameters = json_decode($request->getContent(), true);

        yield new ProfileUpdateDTO(
            $parameters['firstname'] ?? null,
            $parameters['lastname'] ?? null,
            $parameters['password'] ?? null,
            $parameters['repeat_password'] ?? null,
        );
    }
}
