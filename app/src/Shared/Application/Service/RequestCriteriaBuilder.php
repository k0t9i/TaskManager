<?php
declare(strict_types=1);

namespace App\Shared\Application\Service;

use App\Shared\Application\DTO\RequestCriteriaDTO;

final class RequestCriteriaBuilder implements RequestCriteriaBuilderInterface
{
    public const PARAM_FILTER = 'filter';
    public const PARAM_ORDER = 'order';
    public const PARAM_PAGE = 'page';

    public function build(array $request): RequestCriteriaDTO
    {
        $filters = $request[self::PARAM_FILTER] ?? [];

        $rawOrders = $request[self::PARAM_ORDER] ?? [];
        $orders = [];
        if (is_array($rawOrders)) {
            foreach ($rawOrders as $rawOrder) {
                $first = substr($rawOrder, 0, 1);
                $name = in_array($first, ['-', '+']) ? substr($rawOrder, 1) : $rawOrder;
                $orders[$name] = $first !== '-';
            }
        }

        $page = (int) ($request[self::PARAM_PAGE] ?? 1);

        return new RequestCriteriaDTO($filters, $orders, $page);
    }
}
