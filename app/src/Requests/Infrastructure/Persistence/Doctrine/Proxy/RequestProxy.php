<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Requests\Domain\Entity\Request;
use App\Requests\Domain\ValueObject\RequestId;
use App\Shared\Domain\ValueObject\DateTime;
use App\Shared\Domain\ValueObject\Requests\RequestStatus;
use App\Shared\Domain\ValueObject\Users\UserId;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use DateTime as PhpDateTime;

final class RequestProxy implements DoctrineProxyCollectionItemInterface
{
    private string $id;
    private string $userId;
    private int $status;
    private PhpDateTime $changeDate;
    private RequestManagerProxy $manager;
    private ?Request $entity = null;

    public function __construct(RequestManagerProxy $parent, Request $entity)
    {
        $this->entity = $entity;
        $this->manager = $parent;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function refresh(): void
    {
        $this->id = $this->entity->getId()->value;
        $this->userId = $this->entity->getUserId()->value;
        $this->status = $this->entity->getStatus()->getScalar();
        $this->changeDate = PhpDateTime::createFromFormat(
            DateTime::DEFAULT_FORMAT,
            $this->entity->getChangeDate()->getValue()
        );
    }

    public function createEntity(): Request
    {
        if ($this->entity === null) {
            $this->entity = new Request(
                new RequestId($this->id),
                new UserId($this->userId),
                RequestStatus::createFromScalar($this->status),
                new DateTime($this->changeDate->format(DateTime::DEFAULT_FORMAT))
            );
        }
        return $this->entity;
    }

    public function getKey(): string
    {
        return $this->id;
    }
}
