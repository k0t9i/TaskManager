<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\Collection\Hashable;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Requests\RequestStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use DateTime as PhpDateTime;

final class RequestProxy implements Hashable
{
    private string $id;
    private string $userId;
    private int $status;
    private PhpDateTime $changeDate;
    private RequestManagerProxy $manager;

    public function getId(): string
    {
        return $this->id;
    }

    public function loadFromEntity(RequestManagerProxy $manager, Request $entity): void
    {
        $this->id = $entity->getId()->value;
        $this->userId = $entity->getUserId()->value;
        $this->status = $entity->getStatus()->getScalar();
        $this->changeDate = PhpDateTime::createFromFormat(
            DateTime::DEFAULT_FORMAT,
            $entity->getChangeDate()->getValue()
        );
        $this->manager = $manager;
    }

    public function createEntity(): Request
    {
        return new Request(
            new RequestId($this->id),
            new UserId($this->userId),
            RequestStatus::createFromScalar($this->status),
            new DateTime($this->changeDate->format(DateTime::DEFAULT_FORMAT))
        );
    }

    public function getHash(): string
    {
        return $this->id;
    }

    public function isEqual(object $other): bool
    {
        if (!($other instanceof Hashable)) {
            return false;
        }
        return $this->getHash() === $other->getHash();
    }
}
