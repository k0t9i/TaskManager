<?php

declare(strict_types=1);

namespace App\Users\Infrastructure\Symfony;

use App\Users\Infrastructure\Symfony\DTO\UserRegisterDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class UserRegisterValueResolver implements ArgumentValueResolverInterface
{
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return UserRegisterDTO::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $parameters = json_decode($request->getContent(), true);

        yield new UserRegisterDTO(
            $parameters['email'] ?? '',
            $parameters['firstname'] ?? '',
            $parameters['lastname'] ?? '',
            $parameters['password'] ?? '',
            $parameters['repeat_password'] ?? ''
        );
    }
}
