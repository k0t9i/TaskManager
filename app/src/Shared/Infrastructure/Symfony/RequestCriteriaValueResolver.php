<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Symfony;

use App\Shared\Application\DTO\RequestCriteriaDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class RequestCriteriaValueResolver implements ArgumentValueResolverInterface
{
    public const PARAM_FILTER = 'filter';
    public const PARAM_ORDER = 'order';
    public const PARAM_PAGE = 'page';

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return RequestCriteriaDTO::class === $argument->getType();
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $parameters = $request->query->all();

        $filters = $parameters[self::PARAM_FILTER] ?? [];

        $rawOrders = $parameters[self::PARAM_ORDER] ?? [];
        $orders = [];
        if (is_array($rawOrders)) {
            foreach ($rawOrders as $rawOrder) {
                $first = substr($rawOrder, 0, 1);
                $name = in_array($first, ['-', '+']) ? substr($rawOrder, 1) : $rawOrder;
                $orders[$name] = '-' !== $first;
            }
        }

        $page = (int) ($parameters[self::PARAM_PAGE] ?? 1);

        yield new RequestCriteriaDTO($filters, $orders, $page);
    }
}
