<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyCollectionItemInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Tasks\Domain\Entity\Task;
use App\Tasks\Domain\ValueObject\TaskLink;
use DateTime as PhpDateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

final class TaskProxy implements DoctrineProxyCollectionItemInterface, DoctrineProxyInterface
{
    private string $id;
    private string $name;
    private string $brief;
    private string $description;
    private PhpDateTime $startDate;
    private PhpDateTime $finishDate;
    private string $ownerId;
    private int $status;
    /**
     * @var Collection|PersistentCollection|TaskLink[]
     */
    private Collection $links;
    private TaskManagerProxy $manager;
    private ?Task $entity = null;

    public function __construct(TaskManagerProxy $owner, Task $entity)
    {
        $this->links = new ArrayCollection();
        $this->manager = $owner;
        $this->entity = $entity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBrief(): string
    {
        return $this->brief;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartDate(): PhpDateTime
    {
        return $this->startDate;
    }

    public function getFinishDate(): PhpDateTime
    {
        return $this->finishDate;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getLinks(): Collection
    {
        return $this->links;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->id = $this->entity->getId()->value;
        $this->name = $this->entity->getInformation()->name->value;
        $this->brief = $this->entity->getInformation()->brief->value;
        $this->description = $this->entity->getInformation()->description->value;
        $this->startDate = $this->entity->getInformation()->startDate->getPhpDateTime();
        $this->finishDate = $this->entity->getInformation()->finishDate->getPhpDateTime();
        $this->ownerId = $this->entity->getOwnerId()->value;
        $this->status = $this->entity->getStatus()->getScalar();
        $loader->loadInto(
            $this->links,
            $this->entity->getLinks(),
            $this
        );
    }

    public function changeEntity(Task $entity): void
    {
        $this->entity = $entity;
    }

    public function getKey(): string
    {
        return $this->id;
    }
}
