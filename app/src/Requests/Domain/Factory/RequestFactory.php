<?php
declare(strict_types=1);

namespace App\Requests\Domain\Factory;

use App\Requests\Domain\DTO\RequestDTO;
use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Owner;
use App\Shared\Domain\ValueObject\UserId;

final class RequestFactory
{
    public function create(RequestDTO $dto) : Request
    {
        return new Request(
            new RequestId($dto->id),
            new Owner(
                new UserId($dto->userId),
                new Email($dto->userEmail),
            ),
            RequestStatusFactory::objectFromScalar($dto->status),
            new DateTime($dto->changeDate),
        );
    }
}
