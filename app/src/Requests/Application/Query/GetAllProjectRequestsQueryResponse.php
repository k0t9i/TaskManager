<?php
declare(strict_types=1);

namespace App\Requests\Application\Query;

use App\Shared\Domain\Bus\Query\QueryResponseInterface;

final class GetAllProjectRequestsQueryResponse implements QueryResponseInterface
{
    /**
     * @var RequestResponse[]
     */
    private readonly array $requests;

    public function __construct(RequestResponse... $requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return RequestResponse[]
     */
    public function getRequests(): array
    {
        return $this->requests;
    }
}
