<?php
declare(strict_types=1);

namespace App\Requests\Infrastructure\Persistence\Doctrine\Proxy;

use App\Requests\Domain\Entity\RequestManager;
use App\Shared\Infrastructure\Persistence\Doctrine\PersistentCollectionLoaderInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineProxyInterface;
use App\Shared\Infrastructure\Persistence\Doctrine\Proxy\DoctrineVersionedProxyInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\PersistentCollection;

final class RequestManagerProxy implements DoctrineVersionedProxyInterface, DoctrineProxyInterface
{
    private string $id;
    private string $projectId;
    private int $status;
    private string $ownerId;
    /**
     * @var Collection|PersistentCollection|RequestManagerParticipantProxy[]
     */
    private Collection $participants;
    /**
     * @var Collection|PersistentCollection|RequestProxy[]
     */
    private Collection $requests;
    private int $version;
    private ?RequestManager $entity = null;

    public function __construct(RequestManager $entity)
    {
        $this->participants = new ArrayCollection();
        $this->requests = new ArrayCollection();
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

    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function getRequests(): Collection
    {
        return $this->requests;
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
        $loader->loadInto(
            $this->participants,
            $this->entity->getParticipants()->getCollection(),
            $this
        );
        $loader->loadInto(
            $this->requests,
            $this->entity->getRequests()->getCollection(),
            $this
        );
    }

    public function changeEntity(RequestManager $entity): void
    {
        $this->entity = $entity;
    }
}
