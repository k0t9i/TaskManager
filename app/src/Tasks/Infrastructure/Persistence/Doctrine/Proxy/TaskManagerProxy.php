<?php

declare(strict_types=1);

namespace App\Tasks\Infrastructure\Persistence\Doctrine\Proxy;

use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use App\Tasks\Domain\Entity\TaskManager;
use DateTime as PhpDateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

final class TaskManagerProxy implements DoctrineVersionedProxyInterface
{
    private string $id;
    private string $projectId;
    private int $status;
    private string $ownerId;
    private PhpDateTime $finishDate;
    /**
     * @var Collection|PersistentCollection|TaskManagerParticipantProxy[]
     */
    private Collection $participants;
    /**
     * @var Collection|PersistentCollection|TaskProxy[]
     */
    private Collection $tasks;
    private int $version;
    private ?TaskManager $entity = null;

    public function __construct(TaskManager $entity)
    {
        $this->participants = new ArrayCollection();
        $this->tasks = new ArrayCollection();
        $this->entity = $entity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProjectId(): string
    {
        return $this->projectId;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getOwnerId(): string
    {
        return $this->ownerId;
    }

    public function getFinishDate(): PhpDateTime
    {
        return $this->finishDate;
    }

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function refresh(PersistentCollectionLoaderInterface $loader): void
    {
        $this->id = $this->entity->getId()->value;
        $this->projectId = $this->entity->getProjectId()->value;
        $this->status = $this->entity->getStatus()->getScalar();
        $this->ownerId = $this->entity->getOwner()->userId->value;
        $this->finishDate = $this->entity->getFinishDate()->getPhpDateTime();
        $loader->loadInto(
            $this->participants,
            $this->entity->getParticipants()->getCollection(),
            $this
        );
        $loader->loadInto(
            $this->tasks,
            $this->entity->getTasks()->getCollection(),
            $this
        );
    }

    public function changeEntity(TaskManager $entity): void
    {
        $this->entity = $entity;
    }
}
