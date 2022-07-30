<?php

declare(strict_types=1);

namespace App\Projects\Domain\ValueObject;

use App\Projects\Domain\Collection\RequestCollection;
use App\Projects\Domain\Entity\Request;
use App\Projects\Domain\Exception\UserAlreadyHasPendingRequestException;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\Exception\RequestNotExistsException;
use App\Shared\Domain\ValueObject\Projects\ProjectId;
use App\Shared\Domain\ValueObject\Users\UserId;

final class Requests
{
    private RequestCollection $requests;

    public function __construct(?RequestCollection $items = null)
    {
        if (null === $items) {
            $this->requests = new RequestCollection();
        } else {
            $this->requests = $items;
        }
    }

    public function ensureUserDoesNotHavePendingRequest(UserId $userId, ProjectId $projectId): void
    {
        /** @var Request $request */
        foreach ($this->requests as $request) {
            if ($request->isPending() && $request->getUserId()->isEqual($userId)) {
                throw new UserAlreadyHasPendingRequestException($userId->value, $projectId->value);
            }
        }
    }

    public function add(Request $request): self
    {
        $result = new self();
        $result->requests = $this->requests->add($request);
        return $result;
    }

    public function ensureRequestExists(RequestId $requestId): void
    {
        if (!$this->requests->hashExists($requestId->getHash())) {
            throw new RequestNotExistsException($requestId->value);
        }
    }

    /**
     * @return Request|Hashable|null
     */
    public function get(RequestId $requestId): ?Request
    {
        return $this->requests->get($requestId->getHash());
    }

    /**
     * @return RequestCollection|Request[]
     */
    public function getCollection(): RequestCollection
    {
        return $this->requests;
    }
}
